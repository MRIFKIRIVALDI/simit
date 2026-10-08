<?php
require dirname(__DIR__) . '/bootstrap.php';
use Simit\Core\Database;
$pdo=Database::connection();$pdo->beginTransaction();
try{
    foreach([['Shared A','shared-a@simit.local'],['Shared B','shared-b@simit.local']] as $u){$pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)')->execute([$u[0],$u[1],password_hash('TestOnly123!',PASSWORD_DEFAULT),'user']);$ids[]=(int)$pdo->lastInsertId();}
    $admin=(int)$pdo->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetchColumn();
    $pdo->prepare("INSERT INTO tasks(number,title,priority,status,created_by) VALUES('TEST-SHARED','Shared task','Sedang','Tersedia',?)")->execute([$admin]);$task=(int)$pdo->lastInsertId();
    $claim=$pdo->prepare("UPDATE tasks SET assignee_id=?,result='done',status='Menunggu Verifikasi' WHERE id=? AND status='Tersedia' AND assignee_id IS NULL");$claim->execute([$ids[0],$task]);if($claim->rowCount()!==1)throw new RuntimeException('User pertama gagal mengambil tugas bersama.');$claim->execute([$ids[1],$task]);if($claim->rowCount()!==0)throw new RuntimeException('Tugas bersama dapat diselesaikan dua user.');
    $pdo->prepare('INSERT INTO messages(sender_id,recipient_id,body) VALUES(?,?,?)')->execute([$ids[0],$ids[1],'Halo']);notify_user($ids[1],'Pesan chat baru','Halo');if(unread_message_count($ids[1])!==1||unread_notification_count($ids[1])<1)throw new RuntimeException('Unread counter gagal.');
    $pdo->rollBack();echo "Communication test passed.\n";
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();fwrite(STDERR,$e->getMessage()."\n");exit(1);}
