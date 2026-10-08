<?php
namespace Simit\Core;

final class Auth
{
    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) return null;
        $stmt = Database::connection()->prepare('SELECT id,name,email,role,is_active,avatar_stored_name FROM users WHERE id=? AND is_active=1');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }

    public static function attempt(string $email, string $password): bool
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE lower(email)=lower(?) AND is_active=1');
        $stmt->execute([trim($email)]); $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) return false;
        session_regenerate_id(true); $_SESSION['user_id'] = $user['id'];
        self::audit('login', 'users', (int)$user['id'], 'Pengguna masuk');
        return true;
    }

    public static function logout(): void { $_SESSION = []; session_regenerate_id(true); }
    public static function requireLogin(): void { if (!self::user()) redirect('login'); }
    public static function isAdmin(): bool { return (self::user()['role'] ?? '') === 'admin'; }
    public static function requireAdmin(): void { self::requireLogin(); if (!self::isAdmin()) { http_response_code(403); View::render('crud/error', ['title'=>'Akses ditolak','message'=>'Tindakan ini hanya tersedia untuk admin.']); exit; } }
    public static function audit(string $action, string $entity, ?int $entityId, string $detail): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO audit_logs(user_id,action,entity_type,entity_id,detail,created_at) VALUES(?,?,?,?,?,CURRENT_TIMESTAMP)');
        $stmt->execute([$_SESSION['user_id'] ?? null,$action,$entity,$entityId,$detail]);
    }
}

