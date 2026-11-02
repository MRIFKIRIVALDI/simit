<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\{Auth,Database};

$name=trim((string)getenv('SIMIT_ADMIN_NAME'))?:'Koordinator IT';
$email=strtolower(trim((string)getenv('SIMIT_ADMIN_EMAIL')));
$password=(string)getenv('SIMIT_ADMIN_PASSWORD');
if (!filter_var($email,FILTER_VALIDATE_EMAIL) || !Auth::passwordIsStrong($password)) {
    fwrite(STDERR,"Tetapkan SIMIT_ADMIN_EMAIL dan SIMIT_ADMIN_PASSWORD (minimal 12 karakter, huruf besar/kecil, angka, simbol).\n");
    exit(2);
}
$pdo=Database::connection();
if ((int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn()>0) {
    fwrite(STDERR,"Admin sudah ada. Ubah password melalui Profil, bukan melalui perintah ini.\n");
    exit(3);
}
$stmt=$pdo->prepare("INSERT INTO users(name,email,password_hash,role,is_active) VALUES(?,?,?,'admin',1)");
$stmt->execute([$name,$email,Auth::hashPassword($password)]);
echo "Admin awal berhasil dibuat. Hapus environment password dari shell setelah selesai.\n";
