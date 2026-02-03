<?php

require __DIR__ . '/../src/Support/helpers.php';
require __DIR__ . '/../src/Database/Connection.php';

use App\Database\Connection;
use function App\Support\load_env;
use function App\Support\env_value;

$env = load_env(__DIR__ . '/../.env');
foreach ($env as $key => $value) {
    putenv($key . '=' . $value);
}

$pdo = Connection::make([
    'host' => env_value('DB_HOST', 'mysql'),
    'port' => env_value('DB_PORT', '3306'),
    'database' => env_value('DB_DATABASE', 'queuing_system'),
    'username' => env_value('DB_USERNAME', 'queuing'),
    'password' => env_value('DB_PASSWORD', 'queuing'),
]);

$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) NOT NULL UNIQUE, executed_at DATETIME NOT NULL)');

$migrationDir = __DIR__ . '/../database/migrations';
$files = glob($migrationDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $filename = basename($file);
    $stmt = $pdo->prepare('SELECT id FROM migrations WHERE filename = ?');
    $stmt->execute([$filename]);
    if ($stmt->fetch()) {
        continue;
    }

    $sql = file_get_contents($file);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if (!$statement) {
            continue;
        }
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $ignorable =
                strpos($message, 'Duplicate column name') !== false ||
                strpos($message, 'Duplicate key name') !== false ||
                strpos($message, 'already exists') !== false;
            if (!$ignorable) {
                throw $e;
            }
        }
    }

    $stmt = $pdo->prepare('INSERT INTO migrations (filename, executed_at) VALUES (?, NOW())');
    $stmt->execute([$filename]);

    echo "Migrated: {$filename}\n";
}
