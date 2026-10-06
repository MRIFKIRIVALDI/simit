<?php
namespace Simit\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo) return self::$pdo;
        $dsn = (string) env('DB_DSN', 'sqlite:storage/database/simit.sqlite');
        if (str_starts_with($dsn, 'sqlite:')) {
            $relative = substr($dsn, 7);
            if (!str_starts_with($relative, '/') && !preg_match('/^[A-Za-z]:/', $relative)) $dsn = 'sqlite:' . BASE_PATH . '/' . $relative;
        }
        self::$pdo = new PDO($dsn, (string) env('DB_USER', ''), (string) env('DB_PASS', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        if (str_starts_with($dsn, 'sqlite:')) self::$pdo->exec('PRAGMA foreign_keys = ON');
        return self::$pdo;
    }
}

