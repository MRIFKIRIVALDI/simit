<?php
namespace Simit\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo) return self::$pdo;
        $driver = strtolower((string) env('DB_CONNECTION', 'mysql'));
        if ($driver === 'mysql') {
            $host = (string) env('DB_HOST', '127.0.0.1');
            $port = (string) env('DB_PORT', '3306');
            $database = (string) env('DB_DATABASE', 'db_aplikasi');
            $charset = (string) env('DB_CHARSET', 'utf8mb4');
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
            $username = (string) env('DB_USERNAME', 'root');
            $password = (string) env('DB_PASSWORD', '');
        } else {
            $dsn = (string) env('DB_DSN', 'sqlite:storage/database/simit.sqlite');
            $relative = substr($dsn, 7);
            if (!str_starts_with($relative, '/') && !preg_match('/^[A-Za-z]:/', $relative)) $dsn = 'sqlite:' . BASE_PATH . '/' . $relative;
            $username = (string) env('DB_USER', '');
            $password = (string) env('DB_PASS', '');
        }
        self::$pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
        if ($driver === 'mysql') self::$pdo->exec("SET time_zone = '+07:00'");
        else self::$pdo->exec('PRAGMA foreign_keys = ON');
        return self::$pdo;
    }
}

