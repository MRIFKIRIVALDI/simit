<?php
require dirname(__DIR__) . '/bootstrap.php';
use Simit\Core\Database;

$pdo = Database::connection();
$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INTEGER PRIMARY KEY AUTOINCREMENT, migration TEXT UNIQUE NOT NULL, applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
$applied = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
foreach (glob(BASE_PATH . '/database/migrations/*.sql') as $file) {
    $name = basename($file); if (in_array($name, $applied, true)) continue;
    $pdo->beginTransaction();
    try { $pdo->exec(file_get_contents($file)); $stmt=$pdo->prepare('INSERT INTO migrations(migration) VALUES(?)'); $stmt->execute([$name]); $pdo->commit(); echo "Applied: $name\n"; }
    catch (Throwable $e) { $pdo->rollBack(); fwrite(STDERR, "Failed $name: {$e->getMessage()}\n"); exit(1); }
}
echo "Database is up to date.\n";

