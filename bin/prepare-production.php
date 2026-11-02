<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\Database;
if (($argv[1] ?? '') !== '--confirm') {
    fwrite(STDERR, "Perintah ini menghapus seluruh data operasional dan akun non-admin.\nJalankan: php bin/prepare-production.php --confirm\n");
    exit(2);
}

$dsn=(string)env('DB_DSN','sqlite:storage/database/simit.sqlite');
if (!str_starts_with($dsn,'sqlite:')) {
    fwrite(STDERR,"Reset aman ini hanya mendukung database SQLite aktif.\n");
    exit(3);
}

$relative=substr($dsn,7);
$databasePath=(str_starts_with($relative,'/')||preg_match('/^[A-Za-z]:/',$relative))?$relative:BASE_PATH.'/'.$relative;
$databasePath=str_replace('/','\\',$databasePath);
if (!is_file($databasePath)) { fwrite(STDERR,"Database tidak ditemukan.\n"); exit(4); }

$backupDir=BASE_PATH.'/storage/backups';
if (!is_dir($backupDir) && !mkdir($backupDir,0770,true) && !is_dir($backupDir)) { throw new RuntimeException('Folder backup tidak dapat dibuat.'); }
$backupPath=$backupDir.'/simit-before-production-'.date('Ymd-His').'.sqlite';
$pdo=Database::connection();
$adminCount=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1")->fetchColumn();
if ($adminCount<1) { fwrite(STDERR,"Dibatalkan: tidak ada akun admin aktif yang dapat dipertahankan.\n"); exit(5); }

$pdo->exec('VACUUM INTO '.$pdo->quote($backupPath));
$nonAdminAvatars=$pdo->query("SELECT avatar_stored_name FROM users WHERE role<>'admin' AND avatar_stored_name IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
$tables=['attachments','task_contributions','task_status_history','task_assets','inspections','software_installations','asset_condition_counts','notifications','messages','import_batches','audit_logs','tasks','pc_details','assets','software','events','locations','asset_categories','login_attempts'];

$pdo->beginTransaction();
try {
    foreach($tables as $table) $pdo->exec('DELETE FROM '.$table);
    $pdo->exec("DELETE FROM users WHERE role<>'admin'");
    $sequenceTables=array_merge($tables,['users']);
    $placeholders=implode(',',array_fill(0,count($sequenceTables),'?'));
    $stmt=$pdo->prepare("DELETE FROM sqlite_sequence WHERE name IN ($placeholders)");
    $stmt->execute($sequenceTables);
    $pdo->commit();
} catch(Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    @unlink($backupPath);
    throw $e;
}

foreach(glob(BASE_PATH.'/storage/uploads/*')?:[] as $file) if(is_file($file)&&basename($file)!=='.gitkeep') @unlink($file);
foreach($nonAdminAvatars as $avatar){$file=BASE_PATH.'/storage/avatars/'.basename((string)$avatar);if(is_file($file))@unlink($file);}
if(session_status()===PHP_SESSION_ACTIVE)session_write_close();
foreach(glob(BASE_PATH.'/storage/sessions/*')?:[] as $file) if(is_file($file)&&basename($file)!=='.gitkeep') @unlink($file);

echo "Database produksi berhasil dibersihkan.\n";
echo "Akun admin aktif dipertahankan: $adminCount\n";
echo "Backup privat: $backupPath\n";
