<?php
require dirname(__DIR__) . '/bootstrap.php';
use Simit\Core\Database;
$pdo=Database::connection();
$required=['users','assets','pc_details','software','tasks','inspections','events','audit_logs'];
$driver=(string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
if($driver==='mysql'){
    $stmt=$pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE()');
    $stmt->execute();$tables=$stmt->fetchAll(PDO::FETCH_COLUMN);
}else{$tables=$pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);}
foreach($required as $table){if(!in_array($table,$tables,true)){fwrite(STDERR,"Missing table: $table\n");exit(1);}}
if((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()<1){fwrite(STDERR,"Missing seeded user\n");exit(1);}
echo "Smoke test passed.\n";

