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
