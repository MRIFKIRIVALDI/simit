<?php
require dirname(__DIR__) . '/bootstrap.php';
use Simit\Core\Database;

$pdo=Database::connection();
$pdo->beginTransaction();
try {
    $pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)')->execute(['Workflow Test','workflow-test@simit.local',password_hash('TestOnly123!',PASSWORD_DEFAULT),'user']);
    $userId=(int)$pdo->lastInsertId();
    $adminId=(int)$pdo->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetchColumn();
    $pdo->prepare("INSERT INTO tasks(number,title,priority,status,assignee_id,created_by) VALUES('TEST-WF','Workflow test','Sedang','Dikerjakan',?,?)")->execute([$userId,$adminId]);
    $taskId=(int)$pdo->lastInsertId();
    $pdo->prepare("UPDATE tasks SET result='Selesai diuji',status='Menunggu Verifikasi' WHERE id=?")->execute([$taskId]);
    $visible=(int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE id=$taskId AND status<>'Menunggu Verifikasi'")->fetchColumn();
    if($visible!==0) throw new RuntimeException('Pending task masih terlihat pada daftar tugas.');
    $pdo->prepare("UPDATE tasks SET status='Selesai',verified_by=?,completed_at=CURRENT_TIMESTAMP WHERE id=? AND status='Menunggu Verifikasi'")->execute([$adminId,$taskId]);
    $status=$pdo->query("SELECT status FROM tasks WHERE id=$taskId")->fetchColumn();
    if($status!=='Selesai') throw new RuntimeException('Approval gagal mengubah status.');
    $pdo->rollBack(); echo "Workflow test passed.\n";
} catch(Throwable $e) {
    if($pdo->inTransaction()) $pdo->rollBack(); fwrite(STDERR,$e->getMessage()."\n"); exit(1);
}
