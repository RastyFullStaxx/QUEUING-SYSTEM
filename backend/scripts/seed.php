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

$services = [
    ['name' => 'Barangay Clearance', 'code' => 'BC'],
    ['name' => 'Business Permit', 'code' => 'BP'],
    ['name' => 'Residency Certificate', 'code' => 'RC'],
    ['name' => 'Health Certificate', 'code' => 'HC'],
];

foreach ($services as $service) {
    $stmt = $pdo->prepare('SELECT id FROM services WHERE code = ?');
    $stmt->execute([$service['code']]);
    if ($stmt->fetch()) {
        continue;
    }
    $stmt = $pdo->prepare('INSERT INTO services (name, code, active, created_at) VALUES (?, ?, 1, NOW())');
    $stmt->execute([$service['name'], $service['code']]);
    echo "Seeded service: {$service['name']}\n";
}

$admins = [
    [
        'name' => 'Super Admin',
        'email' => 'super.admin@brgysm.local',
        'password' => 'password123',
        'role' => 'super_admin',
        'service_ids' => null,
    ],
    [
        'name' => 'Staff Admin',
        'email' => 'staff.admin@brgysm.local',
        'password' => 'password123',
        'role' => 'staff_admin',
        'service_ids' => json_encode([1]),
    ],
];

foreach ($admins as $admin) {
    $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
    $stmt->execute([$admin['email']]);
    if ($stmt->fetch()) {
        continue;
    }

    $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, role, service_ids, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $admin['name'],
        $admin['email'],
        $hash,
        $admin['role'],
        $admin['service_ids'],
    ]);
    echo "Seeded admin: {$admin['email']}\n";
}
