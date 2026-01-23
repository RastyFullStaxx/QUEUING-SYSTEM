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

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$tables = [
    'audit_logs',
    'queue_tickets',
    'kiosk_devices',
    'resident_ids',
    'admins',
    'residents',
    'services',
];
foreach ($tables as $table) {
    $pdo->exec('TRUNCATE TABLE ' . $table);
}
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

function seed_token(int $bytes = 8): string
{
    return strtoupper(bin2hex(random_bytes($bytes)));
}

function seed_datetime(string $modifier): string
{
    return date('Y-m-d H:i:s', strtotime($modifier));
}

function seed_date(string $modifier): string
{
    return date('Y-m-d', strtotime($modifier));
}

$services = [
    ['name' => 'Proof of residency', 'code' => 'PROOF_RESIDENCY'],
    ['name' => 'Barangay Indigency', 'code' => 'INDIGENCY'],
    ['name' => 'Loan of income', 'code' => 'INCOME_LOAN'],
    ['name' => 'Certificate of Solo parent', 'code' => 'SOLO_PARENT'],
    ['name' => 'Special permit', 'code' => 'SPECIAL_PERMIT'],
    ['name' => 'Building permit', 'code' => 'BUILDING_PERMIT'],
];

$serviceIds = [];
foreach ($services as $service) {
    $stmt = $pdo->prepare('INSERT INTO services (name, code, active, created_at) VALUES (?, ?, 1, NOW())');
    $stmt->execute([$service['name'], $service['code']]);
    $serviceIds[$service['code']] = (int) $pdo->lastInsertId();
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
        'service_ids' => json_encode([$serviceIds['PROOF_RESIDENCY'] ?? 1]),
    ],
];

$superAdminId = null;
foreach ($admins as $admin) {
    $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, role, service_ids, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $admin['name'],
        $admin['email'],
        $hash,
        $admin['role'],
        $admin['service_ids'],
    ]);
    if ($admin['role'] === 'super_admin') {
        $superAdminId = (int) $pdo->lastInsertId();
    }
    echo "Seeded admin: {$admin['email']}\n";
}

$residents = [
    [
        'first_name' => 'Ana',
        'last_name' => 'Santos',
        'email' => 'ana.santos@example.com',
        'password' => 'password123',
        'status' => 'approved',
    ],
    [
        'first_name' => 'Marco',
        'last_name' => 'Reyes',
        'email' => 'marco.reyes@example.com',
        'password' => 'password123',
        'status' => 'approved',
    ],
    [
        'first_name' => 'Liza',
        'last_name' => 'Cruz',
        'email' => 'liza.cruz@example.com',
        'password' => 'password123',
        'status' => 'pending',
    ],
    [
        'first_name' => 'Jose',
        'last_name' => 'Dela Cruz',
        'email' => 'jose.delacruz@example.com',
        'password' => 'password123',
        'status' => 'rejected',
    ],
    [
        'first_name' => 'Bella',
        'last_name' => 'Castro',
        'email' => 'bella.castro@example.com',
        'password' => 'password123',
        'status' => 'approved',
    ],
];

$residentIds = [];
foreach ($residents as $resident) {
    $hash = password_hash($resident['password'], PASSWORD_DEFAULT);
    $qrToken = seed_token();
    $stmt = $pdo->prepare('INSERT INTO residents (first_name, last_name, email, password_hash, qr_token, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([
        $resident['first_name'],
        $resident['last_name'],
        $resident['email'],
        $hash,
        $qrToken,
        $resident['status'],
    ]);
    $residentIds[$resident['email']] = (int) $pdo->lastInsertId();
    echo "Seeded resident: {$resident['email']}\n";
}

$residentIdsData = [
    [
        'email' => 'ana.santos@example.com',
        'valid_id_url' => 'uploads/ids/ana-santos.png',
        'verification_status' => 'approved',
        'created_at' => seed_datetime('-10 days'),
    ],
    [
        'email' => 'liza.cruz@example.com',
        'valid_id_url' => 'uploads/ids/liza-cruz.png',
        'verification_status' => 'pending',
        'created_at' => seed_datetime('-3 days'),
    ],
];

foreach ($residentIdsData as $entry) {
    $residentId = $residentIds[$entry['email']] ?? null;
    if (!$residentId) {
        continue;
    }
    $stmt = $pdo->prepare('INSERT INTO resident_ids (resident_id, valid_id_url, verification_status, created_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $residentId,
        $entry['valid_id_url'],
        $entry['verification_status'],
        $entry['created_at'],
    ]);
    echo "Seeded resident ID: {$entry['email']}\n";
}

$kiosks = [
    [
        'device_id' => 'KIOSK-001',
        'name' => 'Barangay Lobby Kiosk',
        'token' => seed_token(12),
        'last_seen_at' => seed_datetime('-5 minutes'),
    ],
    [
        'device_id' => 'KIOSK-002',
        'name' => 'Annex Kiosk',
        'token' => seed_token(12),
        'last_seen_at' => seed_datetime('-45 minutes'),
    ],
];

$kioskIds = [];
foreach ($kiosks as $kiosk) {
    $stmt = $pdo->prepare('INSERT INTO kiosk_devices (device_id, name, token, last_seen_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $kiosk['device_id'],
        $kiosk['name'],
        $kiosk['token'],
        $kiosk['last_seen_at'],
    ]);
    $kioskIds[$kiosk['device_id']] = (int) $pdo->lastInsertId();
    echo "Seeded kiosk: {$kiosk['device_id']}\n";
}

$tickets = [
    [
        'ticket_no' => 'A-1001',
        'resident_email' => 'ana.santos@example.com',
        'service_code' => 'PROOF_RESIDENCY',
        'status' => 'waiting',
        'issued_at' => '-20 minutes',
        'kiosk_device_id' => 'KIOSK-001',
        'idempotency_key' => 'seed-ticket-1',
    ],
    [
        'ticket_no' => 'A-1002',
        'resident_email' => 'marco.reyes@example.com',
        'service_code' => 'INDIGENCY',
        'status' => 'serving',
        'issued_at' => '-12 minutes',
        'kiosk_device_id' => 'KIOSK-001',
        'idempotency_key' => 'seed-ticket-2',
    ],
    [
        'ticket_no' => 'A-1003',
        'resident_email' => 'bella.castro@example.com',
        'service_code' => 'BUILDING_PERMIT',
        'status' => 'waiting',
        'issued_at' => '-10 minutes',
        'kiosk_device_id' => 'KIOSK-002',
        'idempotency_key' => 'seed-ticket-3',
    ],
    [
        'ticket_no' => 'B-2001',
        'resident_email' => 'ana.santos@example.com',
        'service_code' => 'SPECIAL_PERMIT',
        'status' => 'done',
        'issued_at' => '-1 day',
        'kiosk_device_id' => 'KIOSK-002',
        'idempotency_key' => 'seed-ticket-4',
    ],
    [
        'ticket_no' => 'B-2002',
        'resident_email' => 'marco.reyes@example.com',
        'service_code' => 'INCOME_LOAN',
        'status' => 'cancelled',
        'issued_at' => '-2 days',
        'kiosk_device_id' => 'KIOSK-002',
        'idempotency_key' => 'seed-ticket-5',
    ],
    [
        'ticket_no' => 'B-2003',
        'resident_email' => 'bella.castro@example.com',
        'service_code' => 'SPECIAL_PERMIT',
        'status' => 'done',
        'issued_at' => '-3 days',
        'kiosk_device_id' => 'KIOSK-001',
        'idempotency_key' => 'seed-ticket-6',
    ],
];

$doneTicketIds = [];
foreach ($tickets as $ticket) {
    $residentId = $residentIds[$ticket['resident_email']] ?? null;
    $serviceId = $serviceIds[$ticket['service_code']] ?? null;
    $kioskId = $kioskIds[$ticket['kiosk_device_id']] ?? null;
    if (!$residentId || !$serviceId) {
        continue;
    }
    $issuedAt = seed_datetime($ticket['issued_at']);
    $issuedDate = seed_date($ticket['issued_at']);

    $stmt = $pdo->prepare('INSERT INTO queue_tickets (ticket_no, resident_id, service_id, status, issued_at, issued_date, kiosk_device_id, idempotency_key, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $ticket['ticket_no'],
        $residentId,
        $serviceId,
        $ticket['status'],
        $issuedAt,
        $issuedDate,
        $kioskId,
        $ticket['idempotency_key'],
    ]);
    $ticketId = (int) $pdo->lastInsertId();
    if ($ticket['status'] === 'done') {
        $doneTicketIds[] = [
            'id' => $ticketId,
            'completed_at' => seed_datetime($ticket['issued_at'] . ' + 18 minutes'),
        ];
    }
    echo "Seeded ticket: {$ticket['ticket_no']}\n";
}

if ($superAdminId) {
    foreach ($doneTicketIds as $ticket) {
        $stmt = $pdo->prepare('INSERT INTO audit_logs (actor_type, actor_id, action, meta_json, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            'admin',
            $superAdminId,
            'queue.updated',
            json_encode(['ticket_id' => $ticket['id'], 'status' => 'done']),
            $ticket['completed_at'],
        ]);
    }
    $stmt = $pdo->prepare('INSERT INTO audit_logs (actor_type, actor_id, action, meta_json, created_at) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        'admin',
        $superAdminId,
        'resident.status.updated',
        json_encode(['status' => 'approved']),
        seed_datetime('-2 hours'),
    ]);
    echo "Seeded audit logs.\n";
}
