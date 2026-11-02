<?php
require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\{Auth,Database,Demo};

$pdo=Database::connection();
$assert=function(bool $condition,string $message):void{if(!$condition){fwrite(STDERR,$message."\n");exit(1);}};
$assert((int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1")->fetchColumn()>=1,'Active admin is missing.');
$assert((int)$pdo->query("SELECT COUNT(*) FROM users WHERE role<>'admin'")->fetchColumn()===0,'Non-admin accounts remain.');
foreach(['assets','software','tasks','messages','attachments','notifications'] as $table)$assert((int)$pdo->query('SELECT COUNT(*) FROM '.$table)->fetchColumn()===0,$table.' was not cleared.');
$assert((int)$pdo->query('SELECT COUNT(*) FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id WHERE a.user_id IS NOT NULL AND u.id IS NULL')->fetchColumn()===0,'Orphan audit records were found.');
$assert(Auth::passwordIsStrong('Simit#Secure2026'),'Strong password policy rejected a valid password.');
$assert(!Auth::passwordIsStrong('password123'),'Weak password policy accepted a weak password.');
$before=(int)$pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
$preview=Demo::preview();
$assert(isset($preview['pc'],$preview['perangkat'],$preview['software'],$preview['tasks']),'Demo preview data is incomplete.');
$assert((int)$pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn()===$before,'Demo modified the production database.');
echo "Security isolation test passed.\n";
