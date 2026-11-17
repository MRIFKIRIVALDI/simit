<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\Database;

$sourcePath=BASE_PATH.'/storage/database/simit.sqlite';
if(!is_file($sourcePath)){fwrite(STDERR,"Database SQLite sumber tidak ditemukan.\n");exit(2);}

$target=Database::connection();
if($target->getAttribute(PDO::ATTR_DRIVER_NAME)!=='mysql'){fwrite(STDERR,"Target aktif bukan MySQL/MariaDB.\n");exit(3);}
if((int)$target->query('SELECT COUNT(*) FROM users')->fetchColumn()>0){fwrite(STDERR,"Target users tidak kosong; migrasi Admin dibatalkan.\n");exit(4);}

$source=new PDO('sqlite:'.$sourcePath,null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$admin=$source->query("SELECT id,name,email,password_hash,role,is_active,created_at,updated_at,phone,bio,employee_id,position,department,address,birth_date,avatar_stored_name,avatar_mime_type FROM users WHERE role='admin' AND is_active=1 ORDER BY id LIMIT 1")->fetch();
if(!$admin){fwrite(STDERR,"Admin aktif tidak ditemukan pada SQLite.\n");exit(5);}

$columns=array_keys($admin);
$marks=implode(',',array_fill(0,count($columns),'?'));
$target->beginTransaction();
try{
    $stmt=$target->prepare('INSERT INTO users('.implode(',',$columns).") VALUES($marks)");
    $stmt->execute(array_values($admin));
    $target->commit();
    echo "Akun Admin aktif berhasil dipindahkan tanpa membuka atau mengubah password.\n";
}catch(Throwable $e){
    if($target->inTransaction())$target->rollBack();
    fwrite(STDERR,"Migrasi Admin gagal: {$e->getMessage()}\n");exit(1);
}
