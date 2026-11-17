<?php
require dirname(__DIR__) . '/bootstrap.php';
use Simit\Core\Database;

$pdo = Database::connection();
$driver=(string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
$pdo->exec($driver==='mysql'
    ? "CREATE TABLE IF NOT EXISTS migrations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255) NOT NULL UNIQUE, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    : 'CREATE TABLE IF NOT EXISTS migrations (id INTEGER PRIMARY KEY AUTOINCREMENT, migration TEXT UNIQUE NOT NULL, applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
$applied = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
$migrationDir=$driver==='mysql'?BASE_PATH.'/database/migrations/mysql':BASE_PATH.'/database/migrations';
foreach (glob($migrationDir . '/*.sql') as $file) {
    $name = basename($file); if (in_array($name, $applied, true)) continue;
    if($driver!=='mysql')$pdo->beginTransaction();
    try {
        $sql=preg_replace('/^\s*--.*$/m','',(string)file_get_contents($file))??'';
        $statements=preg_split('/;\s*(?:\R|$)/',$sql)?:[];
        foreach($statements as $statement){$statement=trim($statement);if($statement!=='')$pdo->exec($statement);}
        $stmt=$pdo->prepare('INSERT INTO migrations(migration) VALUES(?)');$stmt->execute([$name]);
        if($driver!=='mysql')$pdo->commit();
        echo "Applied: $name\n";
    }
    catch (Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); fwrite(STDERR, "Failed $name: {$e->getMessage()}\n"); exit(1); }
}
echo "Database is up to date.\n";

