<?php

use App\Database\Connection;
use function App\Support\load_env;
use function App\Support\env_value;
use function App\Support\json_response;
use function App\Support\read_json_body;
use function App\Support\issue_token;
use function App\Support\verify_token;

require __DIR__ . '/../src/Support/helpers.php';
require __DIR__ . '/../src/Database/Connection.php';

$env = load_env(__DIR__ . '/../.env');
foreach ($env as $key => $value) {
    putenv($key . '=' . $value);
}

$appKey = env_value('APP_KEY', 'change_me');

$pdo = Connection::make([
    'host' => env_value('DB_HOST', 'mysql'),
    'port' => env_value('DB_PORT', '3306'),
    'database' => env_value('DB_DATABASE', 'queuing_system'),
    'username' => env_value('DB_USERNAME', 'queuing'),
    'password' => env_value('DB_PASSWORD', 'queuing'),
]);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$allowedOrigins = array_filter(array_map('trim', explode(',', env_value('CORS_ALLOWED_ORIGINS', ''))));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function get_bearer_token(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
        return trim($matches[1]);
    }
    return null;
}

function require_auth(string $type, string $appKey): ?array
{
    $token = get_bearer_token();
    $payload = verify_token($token, $appKey);
    if (!$payload || ($payload['type'] ?? null) !== $type) {
        json_response(['error' => 'Unauthorized'], 401);
        return null;
    }
    return $payload;
}

function require_admin_role(PDO $pdo, int $adminId, string $role): bool
{
    $stmt = $pdo->prepare('SELECT role FROM admins WHERE id = ?');
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    if (!$admin || $admin['role'] !== $role) {
        json_response(['error' => 'Forbidden'], 403);
        return false;
    }
    return true;
}

function log_audit(PDO $pdo, string $actorType, int $actorId, string $action, array $meta = []): void
{
    $stmt = $pdo->prepare('INSERT INTO audit_logs (actor_type, actor_id, action, meta_json, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([
        $actorType,
        $actorId,
        $action,
        $meta ? json_encode($meta) : null,
    ]);
}

if ($path === '/api/health' && $method === 'GET') {
    json_response(['status' => 'ok']);
    exit;
}

if ($path === '/api/auth/resident/register' && $method === 'POST') {
    $body = read_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';
    $firstName = trim($body['first_name'] ?? '');
    $lastName = trim($body['last_name'] ?? '');

    if (!$email || !$password) {
        json_response(['error' => 'Email and password are required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM residents WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['error' => 'Email already registered'], 409);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO residents (first_name, last_name, email, password_hash, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([$firstName, $lastName, $email, $hash, 'pending']);
    $residentId = (int) $pdo->lastInsertId();
    $token = issue_token('resident', $residentId, $appKey);

    json_response([
        'token' => $token,
        'resident' => [
            'id' => $residentId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'status' => 'pending',
        ],
    ], 201);
    exit;
}

if ($path === '/api/auth/resident/login' && $method === 'POST') {
    $body = read_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM residents WHERE email = ?');
    $stmt->execute([$email]);
    $resident = $stmt->fetch();
    if (!$resident || !password_verify($password, $resident['password_hash'])) {
        json_response(['error' => 'Invalid credentials'], 401);
        exit;
    }

    $token = issue_token('resident', (int) $resident['id'], $appKey);
    json_response([
        'token' => $token,
        'resident' => [
            'id' => (int) $resident['id'],
            'first_name' => $resident['first_name'],
            'last_name' => $resident['last_name'],
            'email' => $resident['email'],
            'status' => $resident['status'],
        ],
    ]);
    exit;
}

if ($path === '/api/resident/me' && $method === 'GET') {
    $payload = require_auth('resident', $appKey);
    if (!$payload) {
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, status FROM residents WHERE id = ?');
    $stmt->execute([(int) $payload['id']]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    json_response(['resident' => $resident]);
    exit;
}

if ($path === '/api/auth/admin/login' && $method === 'POST') {
    $body = read_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        json_response(['error' => 'Invalid credentials'], 401);
        exit;
    }

    $token = issue_token('admin', (int) $admin['id'], $appKey);
    json_response([
        'token' => $token,
        'admin' => [
            'id' => (int) $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'role' => $admin['role'],
            'service_ids' => $admin['service_ids'] ? json_decode($admin['service_ids'], true) : [],
        ],
    ]);
    exit;
}

if ($path === '/api/admin/me' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, name, email, role, service_ids FROM admins WHERE id = ?');
    $stmt->execute([(int) $payload['id']]);
    $admin = $stmt->fetch();
    if (!$admin) {
        json_response(['error' => 'Admin not found'], 404);
        exit;
    }

    $admin['service_ids'] = $admin['service_ids'] ? json_decode($admin['service_ids'], true) : [];
    json_response(['admin' => $admin]);
    exit;
}

if ($path === '/api/admin/residents' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $status = $_GET['status'] ?? null;
    $search = trim($_GET['search'] ?? '');
    $query = 'SELECT id, first_name, last_name, email, status, created_at FROM residents';
    $conditions = [];
    $params = [];

    if ($status) {
        $conditions[] = 'status = ?';
        $params[] = $status;
    }
    if ($search) {
        $conditions[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
        $term = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }
    if ($conditions) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY created_at DESC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $residents = $stmt->fetchAll();
    json_response(['residents' => $residents]);
    exit;
}

if (preg_match('#^/api/admin/residents/(\\d+)/status$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $body = read_json_body();
    $status = $body['status'] ?? '';
    if (!in_array($status, ['approved', 'rejected', 'pending'], true)) {
        json_response(['error' => 'Invalid status'], 422);
        exit;
    }

    $residentId = (int) $matches[1];
    $stmt = $pdo->prepare('UPDATE residents SET status = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$status, $residentId]);

    log_audit($pdo, 'admin', (int) $payload['id'], 'resident.status.updated', [
        'resident_id' => $residentId,
        'status' => $status,
    ]);

    $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, status, created_at FROM residents WHERE id = ?');
    $stmt->execute([$residentId]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    json_response(['resident' => $resident]);
    exit;
}

if ($path === '/api/admin/services' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $stmt = $pdo->query('SELECT id, name, code, active, created_at FROM services ORDER BY name');
    json_response(['services' => $stmt->fetchAll()]);
    exit;
}

if ($path === '/api/admin/services' && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $body = read_json_body();
    $name = trim($body['name'] ?? '');
    $code = trim($body['code'] ?? '');
    $active = (int) ($body['active'] ?? 1);
    if (!$name || !$code) {
        json_response(['error' => 'name and code are required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO services (name, code, active, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$name, $code, $active ? 1 : 0]);
    $serviceId = (int) $pdo->lastInsertId();
    log_audit($pdo, 'admin', (int) $payload['id'], 'service.created', ['service_id' => $serviceId]);

    $stmt = $pdo->prepare('SELECT id, name, code, active, created_at FROM services WHERE id = ?');
    $stmt->execute([$serviceId]);
    json_response(['service' => $stmt->fetch()], 201);
    exit;
}

if (preg_match('#^/api/admin/services/(\\d+)$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $serviceId = (int) $matches[1];
    $body = read_json_body();
    $name = trim($body['name'] ?? '');
    $code = trim($body['code'] ?? '');
    $active = $body['active'] ?? null;

    $stmt = $pdo->prepare('SELECT id FROM services WHERE id = ?');
    $stmt->execute([$serviceId]);
    if (!$stmt->fetch()) {
        json_response(['error' => 'Service not found'], 404);
        exit;
    }

    $updates = [];
    $params = [];
    if ($name !== '') {
        $updates[] = 'name = ?';
        $params[] = $name;
    }
    if ($code !== '') {
        $updates[] = 'code = ?';
        $params[] = $code;
    }
    if ($active !== null) {
        $updates[] = 'active = ?';
        $params[] = (int) $active ? 1 : 0;
    }
    if (!$updates) {
        json_response(['error' => 'No updates provided'], 422);
        exit;
    }
    $params[] = $serviceId;
    $stmt = $pdo->prepare('UPDATE services SET ' . implode(', ', $updates) . ' WHERE id = ?');
    $stmt->execute($params);

    log_audit($pdo, 'admin', (int) $payload['id'], 'service.updated', ['service_id' => $serviceId]);
    $stmt = $pdo->prepare('SELECT id, name, code, active, created_at FROM services WHERE id = ?');
    $stmt->execute([$serviceId]);
    json_response(['service' => $stmt->fetch()]);
    exit;
}

if ($path === '/api/admin/users' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $stmt = $pdo->query('SELECT id, name, email, role, service_ids, created_at FROM admins ORDER BY created_at DESC');
    $admins = $stmt->fetchAll();
    foreach ($admins as &$admin) {
        $admin['service_ids'] = $admin['service_ids'] ? json_decode($admin['service_ids'], true) : [];
    }
    json_response(['admins' => $admins]);
    exit;
}

if ($path === '/api/admin/users' && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $body = read_json_body();
    $name = trim($body['name'] ?? '');
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';
    $role = $body['role'] ?? 'staff_admin';
    $serviceIds = $body['service_ids'] ?? [];

    if (!$name || !$email || !$password) {
        json_response(['error' => 'name, email, and password are required'], 422);
        exit;
    }
    if (!in_array($role, ['staff_admin', 'super_admin'], true)) {
        json_response(['error' => 'Invalid role'], 422);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, role, service_ids, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $name,
        $email,
        $hash,
        $role,
        $serviceIds ? json_encode($serviceIds) : null,
    ]);
    $adminId = (int) $pdo->lastInsertId();
    log_audit($pdo, 'admin', (int) $payload['id'], 'admin.created', ['admin_id' => $adminId]);

    $stmt = $pdo->prepare('SELECT id, name, email, role, service_ids, created_at FROM admins WHERE id = ?');
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    $admin['service_ids'] = $admin['service_ids'] ? json_decode($admin['service_ids'], true) : [];
    json_response(['admin' => $admin], 201);
    exit;
}

if (preg_match('#^/api/admin/users/(\\d+)$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $adminId = (int) $matches[1];
    $body = read_json_body();
    $name = trim($body['name'] ?? '');
    $email = strtolower(trim($body['email'] ?? ''));
    $role = $body['role'] ?? null;
    $serviceIds = $body['service_ids'] ?? null;
    $password = $body['password'] ?? null;

    $updates = [];
    $params = [];
    if ($name !== '') {
        $updates[] = 'name = ?';
        $params[] = $name;
    }
    if ($email !== '') {
        $updates[] = 'email = ?';
        $params[] = $email;
    }
    if ($role) {
        if (!in_array($role, ['staff_admin', 'super_admin'], true)) {
            json_response(['error' => 'Invalid role'], 422);
            exit;
        }
        $updates[] = 'role = ?';
        $params[] = $role;
    }
    if ($serviceIds !== null) {
        $updates[] = 'service_ids = ?';
        $params[] = $serviceIds ? json_encode($serviceIds) : null;
    }
    if ($password) {
        $updates[] = 'password_hash = ?';
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }
    if (!$updates) {
        json_response(['error' => 'No updates provided'], 422);
        exit;
    }

    $params[] = $adminId;
    $stmt = $pdo->prepare('UPDATE admins SET ' . implode(', ', $updates) . ' WHERE id = ?');
    $stmt->execute($params);
    log_audit($pdo, 'admin', (int) $payload['id'], 'admin.updated', ['admin_id' => $adminId]);

    $stmt = $pdo->prepare('SELECT id, name, email, role, service_ids, created_at FROM admins WHERE id = ?');
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    if (!$admin) {
        json_response(['error' => 'Admin not found'], 404);
        exit;
    }
    $admin['service_ids'] = $admin['service_ids'] ? json_decode($admin['service_ids'], true) : [];
    json_response(['admin' => $admin]);
    exit;
}

if ($path === '/api/admin/kiosks' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $stmt = $pdo->query('SELECT id, device_id, name, token, last_seen_at FROM kiosk_devices ORDER BY name');
    json_response(['kiosks' => $stmt->fetchAll()]);
    exit;
}

if ($path === '/api/admin/kiosks' && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $body = read_json_body();
    $deviceId = trim($body['device_id'] ?? '');
    $name = trim($body['name'] ?? '');
    if (!$deviceId || !$name) {
        json_response(['error' => 'device_id and name are required'], 422);
        exit;
    }

    $token = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare('INSERT INTO kiosk_devices (device_id, name, token, last_seen_at) VALUES (?, ?, ?, NULL)');
    $stmt->execute([$deviceId, $name, $token]);
    $kioskId = (int) $pdo->lastInsertId();
    log_audit($pdo, 'admin', (int) $payload['id'], 'kiosk.created', ['kiosk_id' => $kioskId]);

    $stmt = $pdo->prepare('SELECT id, device_id, name, token, last_seen_at FROM kiosk_devices WHERE id = ?');
    $stmt->execute([$kioskId]);
    json_response(['kiosk' => $stmt->fetch()], 201);
    exit;
}

if (preg_match('#^/api/admin/kiosks/(\\d+)$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }
    if (!require_admin_role($pdo, (int) $payload['id'], 'super_admin')) {
        exit;
    }

    $kioskId = (int) $matches[1];
    $body = read_json_body();
    $deviceId = trim($body['device_id'] ?? '');
    $name = trim($body['name'] ?? '');
    $token = $body['token'] ?? null;

    $updates = [];
    $params = [];
    if ($deviceId !== '') {
        $updates[] = 'device_id = ?';
        $params[] = $deviceId;
    }
    if ($name !== '') {
        $updates[] = 'name = ?';
        $params[] = $name;
    }
    if ($token !== null) {
        $updates[] = 'token = ?';
        $params[] = $token;
    }
    if (!$updates) {
        json_response(['error' => 'No updates provided'], 422);
        exit;
    }

    $params[] = $kioskId;
    $stmt = $pdo->prepare('UPDATE kiosk_devices SET ' . implode(', ', $updates) . ' WHERE id = ?');
    $stmt->execute($params);
    log_audit($pdo, 'admin', (int) $payload['id'], 'kiosk.updated', ['kiosk_id' => $kioskId]);

    $stmt = $pdo->prepare('SELECT id, device_id, name, token, last_seen_at FROM kiosk_devices WHERE id = ?');
    $stmt->execute([$kioskId]);
    $kiosk = $stmt->fetch();
    if (!$kiosk) {
        json_response(['error' => 'Kiosk not found'], 404);
        exit;
    }
    json_response(['kiosk' => $kiosk]);
    exit;
}

if ($path === '/api/admin/queue' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $serviceId = (int) ($_GET['service_id'] ?? 0);
    $status = $_GET['status'] ?? null;
    $query = 'SELECT id, ticket_no, resident_id, service_id, status, issued_at FROM queue_tickets';
    $conditions = [];
    $params = [];

    if ($serviceId) {
        $conditions[] = 'service_id = ?';
        $params[] = $serviceId;
    }
    if ($status) {
        $conditions[] = 'status = ?';
        $params[] = $status;
    }
    if ($conditions) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY issued_at ASC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    json_response(['tickets' => $stmt->fetchAll()]);
    exit;
}

if ($path === '/api/admin/queue/next' && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $body = read_json_body();
    $serviceId = (int) ($body['service_id'] ?? 0);
    if (!$serviceId) {
        json_response(['error' => 'service_id is required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM queue_tickets WHERE service_id = ? AND status = ? ORDER BY issued_at ASC LIMIT 1');
    $stmt->execute([$serviceId, 'waiting']);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        json_response(['error' => 'No waiting tickets'], 404);
        exit;
    }

    $ticketId = (int) $ticket['id'];
    $stmt = $pdo->prepare('UPDATE queue_tickets SET status = ? WHERE id = ?');
    $stmt->execute(['serving', $ticketId]);
    log_audit($pdo, 'admin', (int) $payload['id'], 'queue.serving', ['ticket_id' => $ticketId]);

    $stmt = $pdo->prepare('SELECT id, ticket_no, resident_id, service_id, status, issued_at FROM queue_tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    json_response(['ticket' => $stmt->fetch()]);
    exit;
}

if (preg_match('#^/api/admin/queue/(\\d+)/(serve|cancel)$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $ticketId = (int) $matches[1];
    $action = $matches[2];
    $status = $action === 'serve' ? 'done' : 'cancelled';

    $stmt = $pdo->prepare('UPDATE queue_tickets SET status = ? WHERE id = ?');
    $stmt->execute([$status, $ticketId]);

    log_audit($pdo, 'admin', (int) $payload['id'], 'queue.updated', [
        'ticket_id' => $ticketId,
        'status' => $status,
    ]);

    $stmt = $pdo->prepare('SELECT id, ticket_no, resident_id, service_id, status, issued_at FROM queue_tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        exit;
    }

    json_response(['ticket' => $ticket]);
    exit;
}

if ($path === '/api/admin/audit-logs' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $limit = (int) ($_GET['limit'] ?? 100);
    $stmt = $pdo->prepare('SELECT id, actor_type, actor_id, action, meta_json, created_at FROM audit_logs ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$limit]);
    $logs = $stmt->fetchAll();
    foreach ($logs as &$log) {
        $log['meta_json'] = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
    }
    json_response(['logs' => $logs]);
    exit;
}

if ($path === '/api/services' && $method === 'GET') {
    $stmt = $pdo->query('SELECT id, name, code, active FROM services WHERE active = 1 ORDER BY name');
    $services = $stmt->fetchAll();
    json_response(['services' => $services]);
    exit;
}

if ($path === '/api/kiosk/validate-qr' && $method === 'POST') {
    $body = read_json_body();
    $residentId = (int) ($body['resident_id'] ?? 0);
    if (!$residentId && !empty($body['qr_code'])) {
        $residentId = (int) $body['qr_code'];
    }

    if (!$residentId) {
        json_response(['error' => 'resident_id or qr_code is required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, first_name, last_name, status FROM residents WHERE id = ?');
    $stmt->execute([$residentId]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['approved' => false, 'resident' => null, 'allowed_services' => []]);
        exit;
    }

    $approved = $resident['status'] === 'approved';
    $services = [];
    if ($approved) {
        $stmt = $pdo->query('SELECT id, name, code FROM services WHERE active = 1 ORDER BY name');
        $services = $stmt->fetchAll();
    }

    json_response([
        'approved' => $approved,
        'resident' => $resident,
        'allowed_services' => $services,
    ]);
    exit;
}

if ($path === '/api/kiosk/tickets' && $method === 'POST') {
    $body = read_json_body();
    $residentId = (int) ($body['resident_id'] ?? 0);
    $serviceId = (int) ($body['service_id'] ?? 0);
    $kioskDeviceId = (int) ($body['kiosk_device_id'] ?? 0);
    $idempotencyKey = trim($body['idempotency_key'] ?? '');

    if (!$residentId || !$serviceId || !$idempotencyKey) {
        json_response(['error' => 'resident_id, service_id, and idempotency_key are required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM queue_tickets WHERE idempotency_key = ?');
    $stmt->execute([$idempotencyKey]);
    $existing = $stmt->fetch();
    if ($existing) {
        json_response(['ticket' => $existing]);
        exit;
    }

    $issuedDate = date('Y-m-d');
    $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM queue_tickets WHERE service_id = ? AND issued_date = ?');
    $stmt->execute([$serviceId, $issuedDate]);
    $count = (int) ($stmt->fetch()['count'] ?? 0);
    $ticketNo = sprintf('%s%03d', $serviceId, $count + 1);

    $stmt = $pdo->prepare('INSERT INTO queue_tickets (ticket_no, resident_id, service_id, status, issued_at, issued_date, kiosk_device_id, idempotency_key, created_at) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, NOW())');
    $stmt->execute([$ticketNo, $residentId, $serviceId, 'waiting', $issuedDate, $kioskDeviceId ?: null, $idempotencyKey]);
    $ticketId = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare('SELECT * FROM queue_tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();

    json_response(['ticket' => $ticket], 201);
    exit;
}

if ($path === '/api/queue/current' && $method === 'GET') {
    $serviceId = (int) ($_GET['service_id'] ?? 0);
    $params = [];
    $query = 'SELECT ticket_no, service_id, status, issued_at FROM queue_tickets WHERE status = "waiting"';
    if ($serviceId) {
        $query .= ' AND service_id = ?';
        $params[] = $serviceId;
    }
    $query .= ' ORDER BY issued_at ASC LIMIT 5';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();

    json_response([
        'now_serving' => null,
        'tickets' => $tickets,
    ]);
    exit;
}

json_response(['error' => 'Not found'], 404);
