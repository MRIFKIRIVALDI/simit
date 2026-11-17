<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\Database;
use Simit\Core\Sql;
if (($argv[1] ?? '') !== '--confirm') {
    fwrite(STDERR, "Perintah ini menghapus seluruh data operasional dan akun non-admin.\nJalankan: php bin/prepare-production.php --confirm\n");
    exit(2);
}

$backupDir=BASE_PATH.'/storage/backups';
if (!is_dir($backupDir) && !mkdir($backupDir,0770,true) && !is_dir($backupDir)) { throw new RuntimeException('Folder backup tidak dapat dibuat.'); }
$pdo=Database::connection();
$driver=(string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
$adminCount=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1")->fetchColumn();
if ($adminCount<1) { fwrite(STDERR,"Dibatalkan: tidak ada akun admin aktif yang dapat dipertahankan.\n"); exit(5); }

if($driver==='mysql'){
    $dumpBinary=(string)env('MYSQLDUMP_BINARY','C:/xampp/mysql/bin/mysqldump.exe');
    if(!is_file($dumpBinary)){fwrite(STDERR,"mysqldump tidak ditemukan. Tetapkan MYSQLDUMP_BINARY.\n");exit(4);}
    $backupPath=$backupDir.'/simit-before-production-'.date('Ymd-His').'.sql';
    $args=[$dumpBinary,'--host='.(string)env('DB_HOST','127.0.0.1'),'--port='.(string)env('DB_PORT','3306'),'--user='.(string)env('DB_USERNAME','root'),'--single-transaction','--routines','--triggers','--default-character-set=utf8mb4'];
    $password=(string)env('DB_PASSWORD','');if($password!=='')$args[]='--password='.$password;$args[]=(string)env('DB_DATABASE','db_aplikasi');
    $pipes=[];$process=proc_open($args,[0=>['pipe','r'],1=>['file',$backupPath,'wb'],2=>['pipe','w']],$pipes,BASE_PATH);
    if(!is_resource($process)){fwrite(STDERR,"mysqldump gagal dijalankan.\n");exit(4);}
    fclose($pipes[0]);$error=stream_get_contents($pipes[2]);fclose($pipes[2]);$exitCode=proc_close($process);
    if($exitCode!==0||!is_file($backupPath)||filesize($backupPath)===0){@unlink($backupPath);fwrite(STDERR,"Backup MySQL gagal: $error\n");exit(4);}
}else{
    $dsn=(string)env('DB_DSN','sqlite:storage/database/simit.sqlite');$relative=substr($dsn,7);$databasePath=(str_starts_with($relative,'/')||preg_match('/^[A-Za-z]:/',$relative))?$relative:BASE_PATH.'/'.$relative;
    if(!is_file($databasePath)){fwrite(STDERR,"Database SQLite tidak ditemukan.\n");exit(4);}
    $backupPath=$backupDir.'/simit-before-production-'.date('Ymd-His').'.sqlite';$backup=$pdo->prepare('VACUUM INTO ?');$backup->execute([$backupPath]);
}
$nonAdminAvatars=$pdo->query("SELECT avatar_stored_name FROM users WHERE role<>'admin' AND avatar_stored_name IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
$tables=['attachments','task_contributions','task_status_history','task_assets','inspections','software_installations','asset_condition_counts','notifications','messages','import_batches','audit_logs','tasks','pc_details','assets','software','events','locations','asset_categories','login_attempts'];

$pdo->beginTransaction();
try {
    foreach($tables as $table) {
        $safeTable=Sql::identifier($table,$tables);
        $pdo->exec('DELETE FROM '.$safeTable);
    }
    $pdo->exec("DELETE FROM users WHERE role<>'admin'");
    if($driver==='sqlite'){
        $sequenceTables=array_merge($tables,['users']);$placeholders=implode(',',array_fill(0,count($sequenceTables),'?'));
        $stmt=$pdo->prepare("DELETE FROM sqlite_sequence WHERE name IN ($placeholders)");$stmt->execute($sequenceTables);
    }
    $pdo->commit();
} catch(Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    @unlink($backupPath);
    throw $e;
}

if($driver==='mysql')foreach(array_merge($tables,['users']) as $table){$safeTable=Sql::identifier($table,array_merge($tables,['users']));$pdo->exec("ALTER TABLE $safeTable AUTO_INCREMENT=1");}

foreach(glob(BASE_PATH.'/storage/uploads/*')?:[] as $file) if(is_file($file)&&basename($file)!=='.gitkeep') @unlink($file);
foreach($nonAdminAvatars as $avatar){$file=BASE_PATH.'/storage/avatars/'.basename((string)$avatar);if(is_file($file))@unlink($file);}
if(session_status()===PHP_SESSION_ACTIVE)session_write_close();
foreach(glob(BASE_PATH.'/storage/sessions/*')?:[] as $file) if(is_file($file)&&basename($file)!=='.gitkeep') @unlink($file);

echo "Database produksi berhasil dibersihkan.\n";
echo "Akun admin aktif dipertahankan: $adminCount\n";
echo "Backup privat: $backupPath\n";
