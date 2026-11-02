<?php
namespace Simit\Core;

final class Auth
{
    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) return null;
        $now=time();
        if (($now-(int)($_SESSION['_last_activity']??$now))>1800 || ($now-(int)($_SESSION['_authenticated_at']??$now))>43200) { self::logout(); return null; }
        $_SESSION['_last_activity']=$now;
        $stmt = Database::connection()->prepare('SELECT id,name,email,role,is_active,avatar_stored_name FROM users WHERE id=? AND is_active=1');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    public static function attempt(string $email, string $password): bool
    {
        $pdo = Database::connection();
        $email = strtolower(trim($email));
        $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? 'cli'), 0, 64);
        $pdo->exec("DELETE FROM login_attempts WHERE created_at < datetime('now','-1 day')");
        $limit = $pdo->prepare("SELECT SUM(CASE WHEN ip_address=? THEN 1 ELSE 0 END) ip_failures,SUM(CASE WHEN ip_address=? AND email=? THEN 1 ELSE 0 END) pair_failures FROM login_attempts WHERE successful=0 AND created_at >= datetime('now','-15 minutes')");
        $limit->execute([$ip, $ip, $email]); $failures=$limit->fetch();
        if ((int)($failures['ip_failures']??0) >= 30 || (int)($failures['pair_failures']??0) >= 8) return false;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE lower(email)=? AND is_active=1');
        $stmt->execute([$email]); $user = $stmt->fetch();
        $valid = $user && password_verify($password, $user['password_hash']);
        $log = $pdo->prepare('INSERT INTO login_attempts(ip_address,email,successful) VALUES(?,?,?)');
        $log->execute([$ip, $email, $valid ? 1 : 0]);
        if (!$valid) return false;
        if (password_needs_rehash($user['password_hash'], self::passwordAlgorithm())) {
            $pdo->prepare('UPDATE users SET password_hash=?,updated_at=CURRENT_TIMESTAMP WHERE id=?')->execute([self::hashPassword($password),$user['id']]);
        }
        session_regenerate_id(true); $_SESSION['user_id'] = $user['id']; $_SESSION['_authenticated_at']=time(); $_SESSION['_last_activity']=time();
        self::audit('login', 'users', (int)$user['id'], 'Pengguna masuk');
        return true;
    }

    public static function logout(): void
    {
        $_SESSION=[];
        if (session_status()===PHP_SESSION_ACTIVE) {
            $params=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);
            session_destroy();
        }
    }
    public static function hashPassword(string $password): string { return password_hash($password,self::passwordAlgorithm()); }
    public static function passwordIsStrong(string $password): bool { return strlen($password)>=12 && (bool)preg_match('/[a-z]/',$password) && (bool)preg_match('/[A-Z]/',$password) && (bool)preg_match('/\d/',$password) && (bool)preg_match('/[^A-Za-z0-9]/',$password); }
    private static function passwordAlgorithm(): string|int { return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT; }
    public static function requireLogin(): void { if (!self::user()) redirect('login'); }
    public static function isAdmin(): bool { return (self::user()['role'] ?? '') === 'admin'; }
    public static function requireAdmin(): void { self::requireLogin(); if (!self::isAdmin()) { http_response_code(403); View::render('crud/error', ['title'=>'Akses ditolak','message'=>'Tindakan ini hanya tersedia untuk admin.']); exit; } }
    public static function audit(string $action, string $entity, ?int $entityId, string $detail): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO audit_logs(user_id,action,entity_type,entity_id,detail,created_at) VALUES(?,?,?,?,?,CURRENT_TIMESTAMP)');
        $stmt->execute([$_SESSION['user_id'] ?? null,$action,$entity,$entityId,$detail]);
    }
}

