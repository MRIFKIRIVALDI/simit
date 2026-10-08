<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);

spl_autoload_register(function (string $class): void {
    $prefix = 'Simit\\';
    if (!str_starts_with($class, $prefix)) return;
    $path = BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) require $path;
});

foreach (is_file(BASE_PATH . '/.env') ? file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [] as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$key, $value] = array_map('trim', explode('=', $line, 2));
    $_ENV[$key] = trim($value, "\"'");
}

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Jakarta');
if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionPath = BASE_PATH . '/storage/sessions';
    if (!is_dir($sessionPath)) mkdir($sessionPath, 0775, true);
    session_save_path($sessionPath);
    session_name('simit_session');
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'use_strict_mode' => true]);
}

function env(string $key, mixed $default = null): mixed { return $_ENV[$key] ?? getenv($key) ?: $default; }
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return '/' . ltrim($path, '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function lang_legacy(): string { return $_SESSION['lang'] ?? 'id'; }
function translate_legacy(string $id, ?string $en = null): string { return lang() === 'en' ? ($en ?? $id) : $id; }
function lang(): string { return $_SESSION['lang'] ?? 'id'; }
function __(string $id, ?string $en = null): string { return lang() === 'en' ? ($en ?? $id) : $id; }
function flash(string $key, ?string $value = null): ?string {
    if ($value !== null) { $_SESSION['_flash'][$key] = $value; return null; }
    $result = $_SESSION['_flash'][$key] ?? null; unset($_SESSION['_flash'][$key]); return $result;
}
function notify_user(int $userId, string $title, string $message, ?string $linkUrl=null): void {
    $stmt=\Simit\Core\Database::connection()->prepare('INSERT INTO notifications(user_id,title,message,link_url) VALUES(?,?,?,?)');
    $stmt->execute([$userId,$title,$message,$linkUrl]);
}
function notify_all_users(string $title, string $message, ?int $exceptId=null, ?string $linkUrl=null): void {
    $pdo=\Simit\Core\Database::connection();$sql='SELECT id FROM users WHERE is_active=1'.($exceptId?' AND id<>?':'');$stmt=$pdo->prepare($sql);$stmt->execute($exceptId?[$exceptId]:[]);
    foreach($stmt->fetchAll(PDO::FETCH_COLUMN) as $id) notify_user((int)$id,$title,$message,$linkUrl);
}
function unread_notification_count(int $userId): int { $s=\Simit\Core\Database::connection()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND read_at IS NULL');$s->execute([$userId]);return (int)$s->fetchColumn(); }
function unread_message_count(int $userId): int { $s=\Simit\Core\Database::connection()->prepare('SELECT COUNT(*) FROM messages WHERE recipient_id=? AND read_at IS NULL');$s->execute([$userId]);return (int)$s->fetchColumn(); }
function simit_icon_legacy(string $name): string {
    $icons = [
        'pc' => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>',
        'perangkat' => '<path d="M12 2l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 17l9 5 9-5"/>',
        'tugas' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
        'pemeriksaan' => '<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3M8 11l2 2 4-4"/>',
        'audit' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$name] ?? $icons['audit']) . '</svg>';
}
function simit_icon(string $name): string {
    $icons = [
        'pc' => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>',
        'perangkat' => '<path d="M12 2l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 17l9 5 9-5"/>',
        'tugas' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
        'pemeriksaan' => '<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3M8 11l2 2 4-4"/>',
        'audit' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$name] ?? $icons['audit']) . '</svg>';
}
function nav_icon(string $name): string {
    if($name==='chat') return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a4 4 0 01-4 4H8l-5 3 1.7-5A8 8 0 013 10a7 7 0 017-7h7a4 4 0 014 4z"/><path d="M8 10h.01M12 10h.01M16 10h.01"/></svg>';
    $icons=['dashboard'=>'<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>','pc'=>'<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>','perangkat'=>'<path d="M12 2l9 5-9 5-9-5 9-5zM3 12l9 5 9-5M3 17l9 5 9-5"/>','software'=>'<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12h8M12 8v8"/>','tugas'=>'<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>','persetujuan'=>'<path d="M8 3h8M9 3a3 3 0 006 0M6 5H5a2 2 0 00-2 2v13a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-1"/><path d="M8 14l3 3 6-7"/>','laporan'=>'<path d="M4 19V9M10 19V5M16 19v-8M22 19H2"/>','profile'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/>','akun'=>'<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/>','audit'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>'];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($icons[$name]??$icons['dashboard']).'</svg>';
}

