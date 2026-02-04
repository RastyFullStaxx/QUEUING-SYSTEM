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
    $queryToken = $_GET['token'] ?? '';
    if ($queryToken) {
        return trim($queryToken);
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

function generate_resident_qr_token(PDO $pdo): string
{
    for ($i = 0; $i < 5; $i++) {
        $token = strtoupper(bin2hex(random_bytes(8)));
        $stmt = $pdo->prepare('SELECT id FROM residents WHERE qr_token = ?');
        $stmt->execute([$token]);
        if (!$stmt->fetch()) {
            return $token;
        }
    }

    return strtoupper(bin2hex(random_bytes(8)));
}

function parse_resident_qr_code(string $code): array
{
    $code = trim($code);
    if ($code === '') {
        return ['resident_id' => 0, 'qr_token' => null];
    }

    if (preg_match('/^BSM\\|RESIDENT\\|(\\d+)\\|(.+)$/i', $code, $matches)) {
        $residentId = (int) $matches[1];
        $token = trim($matches[2]);
        if (stripos($token, 'BSM-QR-') === 0) {
            $token = substr($token, 7);
        }
        $token = preg_replace('/[^a-z0-9]/i', '', $token);
        $token = $token !== '' ? strtoupper($token) : null;

        return ['resident_id' => $residentId, 'qr_token' => $token];
    }

    if (preg_match('/^BSM-QR-([A-Z0-9]+)$/i', $code, $matches)) {
        return ['resident_id' => 0, 'qr_token' => strtoupper($matches[1])];
    }

    if (preg_match('/^BSM-RES-(\\d+)$/i', $code, $matches)) {
        return ['resident_id' => (int) $matches[1], 'qr_token' => null];
    }

    if (preg_match('/^\\d+$/', $code)) {
        return ['resident_id' => (int) $code, 'qr_token' => null];
    }

    return ['resident_id' => 0, 'qr_token' => null];
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

function compute_time_stats(array $values): array
{
    $count = count($values);
    if (!$count) {
        return [
            'count' => 0,
            'avg_minutes' => null,
            'median_minutes' => null,
            'p90_minutes' => null,
            'min_minutes' => null,
            'max_minutes' => null,
        ];
    }
    sort($values);
    $sum = array_sum($values);
    $avg = $sum / $count;
    $medianIndex = (int) floor(($count - 1) / 2);
    if ($count % 2 === 0) {
        $median = ($values[$medianIndex] + $values[$medianIndex + 1]) / 2;
    } else {
        $median = $values[$medianIndex];
    }
    $p90Index = (int) max(0, ceil($count * 0.9) - 1);
    $p90 = $values[$p90Index];
    return [
        'count' => $count,
        'avg_minutes' => round($avg, 1),
        'median_minutes' => round($median, 1),
        'p90_minutes' => round($p90, 1),
        'min_minutes' => round($values[0], 1),
        'max_minutes' => round($values[$count - 1], 1),
    ];
}

function sanitize_docx_replacement($value): string
{
    $value = (string) $value;
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
        if ($converted !== false) {
            $value = $converted;
        }
    }
    $value = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/u', '', $value);
    $value = preg_replace('/[^\\x09\\x0A\\x0D\\x20-\\x{D7FF}\\x{E000}-\\x{FFFD}\\x{10000}-\\x{10FFFF}]/u', '', $value);
    $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
    return $value;
}

function build_docx_text_map(array $nodes): array
{
    $text = '';
    $map = [];
    $offset = 0;
    foreach ($nodes as $node) {
        $value = (string) ($node->nodeValue ?? '');
        $length = strlen($value);
        $map[] = [
            'node' => $node,
            'text' => $value,
            'start' => $offset,
            'end' => $offset + $length,
        ];
        $text .= $value;
        $offset += $length;
    }
    return [$text, $map];
}

function replace_docx_token_in_nodes(array $nodes, string $token, string $replacement): bool
{
    if ($token === '') {
        return false;
    }

    $changed = false;
    $offset = 0;
    $iterations = 0;
    $maxIterations = 1000;

    while ($iterations < $maxIterations) {
        [$fullText, $map] = build_docx_text_map($nodes);
        $pos = strpos($fullText, $token, $offset);
        if ($pos === false) {
            break;
        }

        $iterations += 1;
        $changed = true;
        $end = $pos + strlen($token);
        $startIndex = null;
        $endIndex = null;

        foreach ($map as $index => $info) {
            if ($startIndex === null && $pos >= $info['start'] && $pos < $info['end']) {
                $startIndex = $index;
            }
            if ($endIndex === null && $end > $info['start'] && $end <= $info['end']) {
                $endIndex = $index;
                break;
            }
        }

        if ($startIndex === null || $endIndex === null) {
            break;
        }

        $startInfo = $map[$startIndex];
        $endInfo = $map[$endIndex];
        $startOffset = $pos - $startInfo['start'];
        $endOffset = $end - $endInfo['start'];

        if ($startIndex === $endIndex) {
            $before = substr($startInfo['text'], 0, $startOffset);
            $after = substr($startInfo['text'], $endOffset);
            $startInfo['node']->nodeValue = $before . $replacement . $after;
        } else {
            $before = substr($startInfo['text'], 0, $startOffset);
            $after = substr($endInfo['text'], $endOffset);
            $startInfo['node']->nodeValue = $before . $replacement . $after;
            for ($i = $startIndex + 1; $i <= $endIndex; $i++) {
                $map[$i]['node']->nodeValue = '';
            }
        }

        $offset = $pos + max(1, strlen($replacement));
    }

    return $changed;
}

function replace_docx_placeholders(string $xml, array $replacements): string
{
    if (!$replacements) {
        return $xml;
    }

    $sanitized = [];
    foreach ($replacements as $token => $value) {
        $sanitized[$token] = sanitize_docx_replacement($value);
    }

    if (class_exists('DOMDocument')) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;
        $previousErrors = libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        if ($loaded) {
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $nodeList = $xpath->query('//w:t');
            if ($nodeList && $nodeList->length > 0) {
                $nodes = [];
                foreach ($nodeList as $node) {
                    $nodes[] = $node;
                }
                $changed = false;
                foreach ($sanitized as $token => $value) {
                    if (replace_docx_token_in_nodes($nodes, $token, $value)) {
                        $changed = true;
                    }
                }
                if ($changed) {
                    $updated = $dom->saveXML();
                    if ($updated !== false) {
                        return $updated;
                    }
                } else {
                    return $xml;
                }
            }
        }
    }

    foreach ($sanitized as $token => $value) {
        $safeValue = htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $xml = str_replace($token, $safeValue, $xml);
    }

    return $xml;
}

function render_docx_template(string $templatePath, array $replacements): ?string
{
    if (!file_exists($templatePath)) {
        return null;
    }

    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive extension is not available.');
    }

    $tmpPath = tempnam(sys_get_temp_dir(), 'docx_');
    if ($tmpPath === false) {
        return null;
    }

    if (!copy($templatePath, $tmpPath)) {
        return null;
    }

    $zip = new ZipArchive();
    if ($zip->open($tmpPath) !== true) {
        return null;
    }

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        if (!$stat || empty($stat['name'])) {
            continue;
        }
        $name = $stat['name'];
        if (strpos($name, 'word/') !== 0) {
            continue;
        }
        if (substr($name, -4) !== '.xml') {
            continue;
        }
        $xml = $zip->getFromName($name);
        if ($xml === false) {
            continue;
        }
        $updatedXml = replace_docx_placeholders($xml, $replacements);
        if ($updatedXml !== $xml) {
            $zip->addFromString($name, $updatedXml);
        }
    }
    $zip->close();

    return $tmpPath;
}

function merge_docx_documents(array $paths): ?string
{
    if (count($paths) <= 1) {
        return $paths[0] ?? null;
    }

    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive extension is not available.');
    }

    $basePath = $paths[0];
    $baseZip = new ZipArchive();
    if ($baseZip->open($basePath) !== true) {
        return null;
    }

    $documentXml = $baseZip->getFromName('word/document.xml');
    if ($documentXml === false) {
        $baseZip->close();
        return null;
    }

    $relsXml = $baseZip->getFromName('word/_rels/document.xml.rels');
    if ($relsXml === false) {
        $relsXml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';
    }

    $contentTypesXml = $baseZip->getFromName('[Content_Types].xml');
    if ($contentTypesXml === false) {
        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"></Types>';
    }

    $docDom = new DOMDocument();
    $docDom->preserveWhiteSpace = false;
    if (!$docDom->loadXML($documentXml)) {
        $baseZip->close();
        return null;
    }
    $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $relNamespace = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    $docXpath = new DOMXPath($docDom);
    $docXpath->registerNamespace('w', $namespace);
    $body = $docXpath->query('/w:document/w:body')->item(0);
    if (!$body) {
        $baseZip->close();
        return null;
    }
    $sectPr = $docXpath->query('w:sectPr', $body)->item(0);
    if (!$sectPr) {
        $sectPr = $docDom->createElementNS($namespace, 'w:sectPr');
        $body->appendChild($sectPr);
    }

    $relsDom = new DOMDocument();
    $relsDom->preserveWhiteSpace = false;
    $relsDom->loadXML($relsXml);
    $relsXpath = new DOMXPath($relsDom);
    $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');
    $relsRoot = $relsDom->documentElement;
    $maxRId = 0;
    foreach ($relsXpath->query('//rel:Relationship') as $relNode) {
        $id = $relNode->getAttribute('Id');
        if (preg_match('/rId(\d+)/', $id, $matches)) {
            $maxRId = max($maxRId, (int) $matches[1]);
        }
    }

    $typesDom = new DOMDocument();
    $typesDom->preserveWhiteSpace = false;
    $typesDom->loadXML($contentTypesXml);
    $typesXpath = new DOMXPath($typesDom);
    $typesXpath->registerNamespace('ct', 'http://schemas.openxmlformats.org/package/2006/content-types');
    $typesRoot = $typesDom->documentElement;

    $chunkIndex = 1;
    foreach (array_slice($paths, 1) as $docPath) {
        if (!is_file($docPath)) {
            continue;
        }
        while ($baseZip->locateName('word/afchunk' . $chunkIndex . '.docx') !== false) {
            $chunkIndex += 1;
        }
        $chunkName = 'afchunk' . $chunkIndex . '.docx';
        $chunkTarget = $chunkName;
        $chunkPartName = 'word/' . $chunkName;

        $baseZip->addFile($docPath, $chunkPartName);

        $newId = 'rId' . (++$maxRId);
        $relationship = $relsDom->createElementNS(
            'http://schemas.openxmlformats.org/package/2006/relationships',
            'Relationship'
        );
        $relationship->setAttribute('Id', $newId);
        $relationship->setAttribute(
            'Type',
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/aFChunk'
        );
        $relationship->setAttribute('Target', $chunkTarget);
        $relsRoot->appendChild($relationship);

        $altChunk = $docDom->createElementNS($namespace, 'w:altChunk');
        $altChunk->setAttributeNS($relNamespace, 'r:id', $newId);
        $body->insertBefore($altChunk, $sectPr);

        $overridePath = '/' . $chunkPartName;
        $existing = $typesXpath->query('//ct:Override[@PartName="' . $overridePath . '"]');
        if ($existing->length === 0) {
            $override = $typesDom->createElementNS(
                'http://schemas.openxmlformats.org/package/2006/content-types',
                'Override'
            );
            $override->setAttribute('PartName', $overridePath);
            $override->setAttribute(
                'ContentType',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            );
            $typesRoot->appendChild($override);
        }

        $chunkIndex += 1;
    }

    $baseZip->addFromString('word/document.xml', $docDom->saveXML());
    $baseZip->addFromString('word/_rels/document.xml.rels', $relsDom->saveXML());
    $baseZip->addFromString('[Content_Types].xml', $typesDom->saveXML());
    $baseZip->close();

    return $basePath;
}

function normalize_service_ids($serviceIds): array
{
    if (!is_array($serviceIds)) {
        return [];
    }
    $unique = [];
    foreach ($serviceIds as $serviceId) {
        $id = (int) $serviceId;
        if ($id > 0) {
            $unique[$id] = $id;
        }
    }
    return array_values($unique);
}

function fetch_services_by_ids(PDO $pdo, array $serviceIds): array
{
    $serviceIds = array_values(array_filter(array_map('intval', $serviceIds)));
    if (!$serviceIds) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($serviceIds), '?'));
    $stmt = $pdo->prepare('SELECT id, name, code FROM services WHERE id IN (' . $placeholders . ')');
    $stmt->execute($serviceIds);
    $rows = $stmt->fetchAll();
    $map = [];
    foreach ($rows as $row) {
        $map[(int) $row['id']] = [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'code' => $row['code'],
        ];
    }
    return $map;
}

function fetch_ticket_services(PDO $pdo, array $ticketIds): array
{
    $ticketIds = array_values(array_filter(array_map('intval', $ticketIds)));
    if (!$ticketIds) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));
    $stmt = $pdo->prepare(
        'SELECT qts.ticket_id, s.id, s.name, s.code
         FROM queue_ticket_services qts
         JOIN services s ON s.id = qts.service_id
         WHERE qts.ticket_id IN (' . $placeholders . ')
         ORDER BY qts.id ASC'
    );
    $stmt->execute($ticketIds);
    $rows = $stmt->fetchAll();
    $map = [];
    foreach ($rows as $row) {
        $ticketId = (int) $row['ticket_id'];
        $map[$ticketId][] = [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'code' => $row['code'],
        ];
    }
    return $map;
}

function attach_services_to_ticket(PDO $pdo, array $ticket): array
{
    $ticketId = (int) ($ticket['id'] ?? 0);
    $serviceMap = $ticketId ? fetch_ticket_services($pdo, [$ticketId]) : [];
    $services = $serviceMap[$ticketId] ?? [];
    if (!$services) {
        $serviceId = (int) ($ticket['service_id'] ?? 0);
        if ($serviceId) {
            $serviceLookup = fetch_services_by_ids($pdo, [$serviceId]);
            $services[] = $serviceLookup[$serviceId] ?? ['id' => $serviceId, 'name' => '', 'code' => ''];
        }
    }
    $ticket['services'] = $services;
    $ticket['service_ids'] = array_values(array_unique(array_filter(array_map(static function ($item) {
        return (int) ($item['id'] ?? 0);
    }, $services))));
    return $ticket;
}

function attach_services_to_tickets(PDO $pdo, array $tickets): array
{
    if (!$tickets) {
        return $tickets;
    }
    $ticketIds = [];
    foreach ($tickets as $ticket) {
        $ticketId = (int) ($ticket['id'] ?? 0);
        if ($ticketId) {
            $ticketIds[] = $ticketId;
        }
    }
    $serviceMap = $ticketIds ? fetch_ticket_services($pdo, $ticketIds) : [];
    $fallbackServiceIds = [];
    foreach ($tickets as $ticket) {
        $ticketId = (int) ($ticket['id'] ?? 0);
        if (!isset($serviceMap[$ticketId])) {
            $fallbackId = (int) ($ticket['service_id'] ?? 0);
            if ($fallbackId) {
                $fallbackServiceIds[$fallbackId] = $fallbackId;
            }
        }
    }
    $serviceLookup = fetch_services_by_ids($pdo, array_values($fallbackServiceIds));

    foreach ($tickets as &$ticket) {
        $ticketId = (int) ($ticket['id'] ?? 0);
        $services = $serviceMap[$ticketId] ?? [];
        if (!$services) {
            $serviceId = (int) ($ticket['service_id'] ?? 0);
            if ($serviceId) {
                $services[] = $serviceLookup[$serviceId] ?? ['id' => $serviceId, 'name' => '', 'code' => ''];
            }
        }
        $ticket['services'] = $services;
        $ticket['service_ids'] = array_values(array_unique(array_filter(array_map(static function ($item) {
            return (int) ($item['id'] ?? 0);
        }, $services))));
    }
    unset($ticket);

    return $tickets;
}

function fetch_queue_ticket(PDO $pdo, int $ticketId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT q.id, q.ticket_no, q.resident_id, q.service_id, q.status, q.issued_at,
                r.username AS resident_username,
                r.first_name AS resident_first_name,
                r.middle_name AS resident_middle_name,
                r.last_name AS resident_last_name,
                r.email AS resident_email,
                r.mobile_number AS resident_mobile_number,
                r.address AS resident_address,
                r.date_of_birth AS resident_date_of_birth,
                r.gender AS resident_gender,
                r.civil_status AS resident_civil_status
         FROM queue_tickets q
         JOIN residents r ON r.id = q.resident_id
         WHERE q.id = ?'
    );
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        return null;
    }
    return attach_services_to_ticket($pdo, $ticket);
}

if ($path === '/api/health' && $method === 'GET') {
    json_response(['status' => 'ok']);
    exit;
}

if ($path === '/api/admin/zip-check' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    json_response([
        'zip_loaded' => extension_loaded('zip'),
        'zip_class' => class_exists('ZipArchive'),
        'php_version' => PHP_VERSION,
        'sapi' => php_sapi_name(),
        'php_ini' => php_ini_loaded_file(),
        'php_ini_scanned' => php_ini_scanned_files(),
        'extension_dir' => ini_get('extension_dir'),
    ]);
    exit;
}

if ($path === '/api/auth/resident/register' && $method === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isMultipart = str_contains($contentType, 'multipart/form-data');
    $body = $isMultipart ? $_POST : read_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';
    $username = trim($body['username'] ?? '');
    $firstName = trim($body['first_name'] ?? '');
    $middleName = trim($body['middle_name'] ?? '');
    $lastName = trim($body['last_name'] ?? '');
    $dateOfBirth = trim($body['date_of_birth'] ?? '');
    $gender = trim($body['gender'] ?? '');
    $civilStatus = trim($body['civil_status'] ?? '');
    $mobileNumber = trim($body['mobile_number'] ?? '');
    $address = trim($body['address'] ?? '');
    $validIdUrl = trim($body['valid_id_url'] ?? '');
    $uploadedId = $isMultipart ? ($_FILES['valid_id'] ?? null) : null;

    if (
        !$email ||
        !$password ||
        !$username ||
        !$firstName ||
        !$lastName ||
        !$dateOfBirth ||
        !$gender ||
        !$civilStatus ||
        !$mobileNumber ||
        !$address
    ) {
        json_response(['error' => 'Complete account, personal, and contact details are required'], 422);
        exit;
    }
    if (!$validIdUrl && !$uploadedId) {
        json_response(['error' => 'Valid ID upload is required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM residents WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['error' => 'Email already registered'], 409);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM residents WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        json_response(['error' => 'Username already taken'], 409);
        exit;
    }

    $fileExtension = null;
    if ($uploadedId) {
        if (($uploadedId['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            json_response(['error' => 'Valid ID upload failed'], 422);
            exit;
        }
        if (($uploadedId['size'] ?? 0) > 5 * 1024 * 1024) {
            json_response(['error' => 'Valid ID must be 5MB or less'], 422);
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($uploadedId['tmp_name']);
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        if (!isset($allowedTypes[$mimeType])) {
            json_response(['error' => 'Valid ID must be a JPG, PNG, WEBP, or PDF'], 422);
            exit;
        }
        $fileExtension = $allowedTypes[$mimeType];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $qrToken = generate_resident_qr_token($pdo);
    $stmt = $pdo->prepare(
        'INSERT INTO residents (username, first_name, middle_name, last_name, date_of_birth, gender, civil_status, mobile_number, address, email, password_hash, qr_token, status, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
    );
    $stmt->execute([
        $username,
        $firstName,
        $middleName !== '' ? $middleName : null,
        $lastName,
        $dateOfBirth,
        $gender,
        $civilStatus,
        $mobileNumber,
        $address,
        $email,
        $hash,
        $qrToken,
        'pending',
    ]);
    $residentId = (int) $pdo->lastInsertId();
    $token = issue_token('resident', $residentId, $appKey);

    if ($uploadedId) {
        $uploadDir = __DIR__ . '/uploads/resident-ids';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            $pdo->prepare('DELETE FROM residents WHERE id = ?')->execute([$residentId]);
            json_response(['error' => 'Unable to store valid ID upload'], 500);
            exit;
        }
        $filename = sprintf(
            'resident_%d_%s.%s',
            $residentId,
            bin2hex(random_bytes(6)),
            $fileExtension
        );
        $destination = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($uploadedId['tmp_name'], $destination)) {
            $pdo->prepare('DELETE FROM residents WHERE id = ?')->execute([$residentId]);
            json_response(['error' => 'Unable to store valid ID upload'], 500);
            exit;
        }
        $validIdUrl = '/uploads/resident-ids/' . $filename;
    }

    if ($validIdUrl) {
        $stmt = $pdo->prepare('INSERT INTO resident_ids (resident_id, valid_id_url, verification_status, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$residentId, $validIdUrl, 'pending']);
    }

    json_response([
        'token' => $token,
        'resident' => [
            'id' => $residentId,
            'username' => $username,
            'first_name' => $firstName,
            'middle_name' => $middleName !== '' ? $middleName : null,
            'last_name' => $lastName,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
            'civil_status' => $civilStatus,
            'mobile_number' => $mobileNumber,
            'address' => $address,
            'email' => $email,
            'status' => 'pending',
            'qr_token' => $qrToken,
            'profile_photo_url' => null,
            'valid_id_url' => $validIdUrl ?: null,
            'verification_status' => $validIdUrl ? 'pending' : null,
        ],
    ], 201);
    exit;
}

if ($path === '/api/auth/resident/login' && $method === 'POST') {
    $body = read_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT r.id, r.username, r.first_name, r.middle_name, r.last_name, r.date_of_birth, r.gender,
                r.civil_status, r.mobile_number, r.address, r.email, r.status, r.qr_token, r.profile_photo_url,
                r.password_hash, ri.valid_id_url, ri.verification_status
         FROM residents r
         LEFT JOIN resident_ids ri ON ri.id = (
            SELECT id FROM resident_ids WHERE resident_id = r.id ORDER BY created_at DESC LIMIT 1
         )
         WHERE r.email = ?'
    );
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
            'username' => $resident['username'],
            'first_name' => $resident['first_name'],
            'middle_name' => $resident['middle_name'],
            'last_name' => $resident['last_name'],
            'date_of_birth' => $resident['date_of_birth'],
            'gender' => $resident['gender'],
            'civil_status' => $resident['civil_status'],
            'mobile_number' => $resident['mobile_number'],
            'address' => $resident['address'],
            'email' => $resident['email'],
            'status' => $resident['status'],
            'qr_token' => $resident['qr_token'],
            'profile_photo_url' => $resident['profile_photo_url'],
            'valid_id_url' => $resident['valid_id_url'],
            'verification_status' => $resident['verification_status'],
        ],
    ]);
    exit;
}

if ($path === '/api/resident/me' && $method === 'GET') {
    $payload = require_auth('resident', $appKey);
    if (!$payload) {
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT r.id, r.username, r.first_name, r.middle_name, r.last_name, r.date_of_birth, r.gender,
                r.civil_status, r.mobile_number, r.address, r.email, r.status, r.qr_token, r.profile_photo_url,
                ri.valid_id_url, ri.verification_status
         FROM residents r
         LEFT JOIN resident_ids ri ON ri.id = (
            SELECT id FROM resident_ids WHERE resident_id = r.id ORDER BY created_at DESC LIMIT 1
         )
         WHERE r.id = ?'
    );
    $stmt->execute([(int) $payload['id']]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    json_response(['resident' => $resident]);
    exit;
}

if ($path === '/api/resident/profile-photo' && $method === 'GET') {
    $payload = require_auth('resident', $appKey);
    if (!$payload) {
        exit;
    }

    $stmt = $pdo->prepare('SELECT profile_photo_url FROM residents WHERE id = ?');
    $stmt->execute([(int) $payload['id']]);
    $resident = $stmt->fetch();
    if (!$resident || !$resident['profile_photo_url']) {
        json_response(['data_url' => null]);
        exit;
    }

    $relativePath = $resident['profile_photo_url'];
    $filePath = __DIR__ . $relativePath;
    if (!is_file($filePath)) {
        json_response(['data_url' => null]);
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($filePath) ?: 'image/jpeg';
    $contents = file_get_contents($filePath);
    if ($contents === false) {
        json_response(['data_url' => null]);
        exit;
    }
    $dataUrl = 'data:' . $mimeType . ';base64,' . base64_encode($contents);
    json_response(['data_url' => $dataUrl]);
    exit;
}

if ($path === '/api/resident/transactions' && $method === 'GET') {
    $payload = require_auth('resident', $appKey);
    if (!$payload) {
        exit;
    }

    $residentId = (int) $payload['id'];
    $limit = (int) ($_GET['limit'] ?? 50);
    $limit = max(1, min($limit, 200));
    $offset = (int) ($_GET['offset'] ?? 0);
    $offset = max(0, $offset);

    $countStmt = $pdo->prepare('SELECT COUNT(*) AS total FROM queue_tickets WHERE resident_id = ?');
    $countStmt->execute([$residentId]);
    $total = (int) ($countStmt->fetch()['total'] ?? 0);

    $stmt = $pdo->prepare(
        'SELECT q.id, q.ticket_no, q.status, q.issued_at, q.service_id,
                s.name AS service_name, s.code AS service_code
         FROM queue_tickets q
         LEFT JOIN services s ON s.id = q.service_id
         WHERE q.resident_id = ?
         ORDER BY q.issued_at DESC
         LIMIT ' . $limit . ' OFFSET ' . $offset
    );
    $stmt->execute([$residentId]);
    $transactions = $stmt->fetchAll();
    $transactions = attach_services_to_tickets($pdo, $transactions);

    json_response([
        'transactions' => $transactions,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
    ]);
    exit;
}

if ($path === '/api/resident/profile' && $method === 'POST') {
    $payload = require_auth('resident', $appKey);
    if (!$payload) {
        exit;
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isMultipart = str_contains($contentType, 'multipart/form-data');
    $body = $isMultipart ? $_POST : read_json_body();
    $uploadedPhoto = $isMultipart ? ($_FILES['profile_photo'] ?? null) : null;
    $uploadedId = $isMultipart ? ($_FILES['valid_id'] ?? null) : null;

    if ($uploadedPhoto && ($uploadedPhoto['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_NO_FILE) {
        $uploadedPhoto = null;
    }
    if ($uploadedId && ($uploadedId['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_NO_FILE) {
        $uploadedId = null;
    }

    $username = trim($body['username'] ?? '');
    $firstName = trim($body['first_name'] ?? '');
    $middleName = trim($body['middle_name'] ?? '');
    $lastName = trim($body['last_name'] ?? '');
    $dateOfBirth = trim($body['date_of_birth'] ?? '');
    $gender = trim($body['gender'] ?? '');
    $civilStatus = trim($body['civil_status'] ?? '');
    $mobileNumber = trim($body['mobile_number'] ?? '');
    $address = trim($body['address'] ?? '');
    $email = strtolower(trim($body['email'] ?? ''));

    if (!$firstName || !$lastName || !$email) {
        json_response(['error' => 'First name, last name, and email are required'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, email, username, profile_photo_url FROM residents WHERE id = ?');
    $stmt->execute([(int) $payload['id']]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    if ($email !== $resident['email']) {
        $stmt = $pdo->prepare('SELECT id FROM residents WHERE email = ? AND id != ?');
        $stmt->execute([$email, (int) $payload['id']]);
        if ($stmt->fetch()) {
            json_response(['error' => 'Email already registered'], 409);
            exit;
        }
    }

    if ($username !== '' && $username !== $resident['username']) {
        $stmt = $pdo->prepare('SELECT id FROM residents WHERE username = ? AND id != ?');
        $stmt->execute([$username, (int) $payload['id']]);
        if ($stmt->fetch()) {
            json_response(['error' => 'Username already taken'], 409);
            exit;
        }
    }

    $profilePhotoUrl = $resident['profile_photo_url'];
    if ($uploadedPhoto) {
        if (($uploadedPhoto['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            json_response(['error' => 'Profile photo upload failed'], 422);
            exit;
        }
        if (($uploadedPhoto['size'] ?? 0) > 5 * 1024 * 1024) {
            json_response(['error' => 'Profile photo must be 5MB or less'], 422);
            exit;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($uploadedPhoto['tmp_name']);
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowedTypes[$mimeType])) {
            json_response(['error' => 'Profile photo must be a JPG, PNG, or WEBP'], 422);
            exit;
        }

        $uploadDir = __DIR__ . '/uploads/resident-photos';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            json_response(['error' => 'Unable to store profile photo upload'], 500);
            exit;
        }
        $filename = sprintf(
            'resident_%d_%s.%s',
            (int) $payload['id'],
            bin2hex(random_bytes(6)),
            $allowedTypes[$mimeType]
        );
        $destination = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($uploadedPhoto['tmp_name'], $destination)) {
            json_response(['error' => 'Unable to store profile photo upload'], 500);
            exit;
        }
        $profilePhotoUrl = '/uploads/resident-photos/' . $filename;
    }

    $validIdUrl = null;
    if ($uploadedId) {
        if (($uploadedId['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            json_response(['error' => 'Valid ID upload failed'], 422);
            exit;
        }
        if (($uploadedId['size'] ?? 0) > 5 * 1024 * 1024) {
            json_response(['error' => 'Valid ID must be 5MB or less'], 422);
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($uploadedId['tmp_name']);
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        if (!isset($allowedTypes[$mimeType])) {
            json_response(['error' => 'Valid ID must be a JPG, PNG, WEBP, or PDF'], 422);
            exit;
        }

        $uploadDir = __DIR__ . '/uploads/resident-ids';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            json_response(['error' => 'Unable to store valid ID upload'], 500);
            exit;
        }
        $filename = sprintf(
            'resident_%d_%s.%s',
            (int) $payload['id'],
            bin2hex(random_bytes(6)),
            $allowedTypes[$mimeType]
        );
        $destination = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($uploadedId['tmp_name'], $destination)) {
            json_response(['error' => 'Unable to store valid ID upload'], 500);
            exit;
        }
        $validIdUrl = '/uploads/resident-ids/' . $filename;
    }

    $stmt = $pdo->prepare(
        'UPDATE residents
         SET username = ?, first_name = ?, middle_name = ?, last_name = ?, date_of_birth = ?, gender = ?, civil_status = ?,
             mobile_number = ?, address = ?, email = ?, profile_photo_url = ?, updated_at = NOW()
         WHERE id = ?'
    );
    $stmt->execute([
        $username !== '' ? $username : null,
        $firstName,
        $middleName !== '' ? $middleName : null,
        $lastName,
        $dateOfBirth !== '' ? $dateOfBirth : null,
        $gender !== '' ? $gender : null,
        $civilStatus !== '' ? $civilStatus : null,
        $mobileNumber !== '' ? $mobileNumber : null,
        $address !== '' ? $address : null,
        $email,
        $profilePhotoUrl,
        (int) $payload['id'],
    ]);

    if ($validIdUrl) {
        $stmt = $pdo->prepare('INSERT INTO resident_ids (resident_id, valid_id_url, verification_status, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([(int) $payload['id'], $validIdUrl, 'pending']);
    }

    $stmt = $pdo->prepare(
        'SELECT r.id, r.username, r.first_name, r.middle_name, r.last_name, r.date_of_birth, r.gender,
                r.civil_status, r.mobile_number, r.address, r.email, r.status, r.qr_token, r.profile_photo_url,
                ri.valid_id_url, ri.verification_status
         FROM residents r
         LEFT JOIN resident_ids ri ON ri.id = (
            SELECT id FROM resident_ids WHERE resident_id = r.id ORDER BY created_at DESC LIMIT 1
         )
         WHERE r.id = ?'
    );
    $stmt->execute([(int) $payload['id']]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    json_response(['resident' => $resident]);
    exit;
}

if ($path === '/api/auth/admin/register' && $method === 'POST') {
    $body = read_json_body();
    $name = trim($body['name'] ?? '');
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';

    if (!$name || !$email || !$password) {
        json_response(['error' => 'Name, email, and password are required'], 422);
        exit;
    }
    if (strlen($password) < 8) {
        json_response(['error' => 'Password must be at least 8 characters'], 422);
        exit;
    }

    $count = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($count > 0) {
        json_response(['error' => 'Registration is disabled. Contact a super admin.'], 403);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['error' => 'Email already registered'], 409);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $serviceIds = json_encode([]);
    $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash, role, service_ids, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $passwordHash, 'super_admin', $serviceIds]);
    $adminId = (int) $pdo->lastInsertId();

    log_audit($pdo, 'admin', $adminId, 'admin.registered', ['admin_id' => $adminId]);

    $token = issue_token('admin', $adminId, $appKey);
    json_response([
        'token' => $token,
        'admin' => [
            'id' => $adminId,
            'name' => $name,
            'email' => $email,
            'role' => 'super_admin',
            'service_ids' => [],
        ],
    ], 201);
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
    $query = 'SELECT r.id, r.username, r.first_name, r.middle_name, r.last_name, r.date_of_birth, r.gender,
                     r.civil_status, r.mobile_number, r.address, r.email, r.status, r.profile_photo_url, r.created_at,
                     ri.valid_id_url, ri.verification_status
              FROM residents r
              LEFT JOIN resident_ids ri ON ri.id = (
                SELECT id FROM resident_ids WHERE resident_id = r.id ORDER BY created_at DESC LIMIT 1
              )';
    $conditions = [];
    $params = [];

    if ($status) {
        $conditions[] = 'status = ?';
        $params[] = $status;
    }
    if ($search) {
        $conditions[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ? OR mobile_number LIKE ?)';
        $term = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }
    if ($conditions) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY r.created_at DESC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $residents = $stmt->fetchAll();
    json_response(['residents' => $residents]);
    exit;
}

if ($path === '/api/admin/residents' && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $body = read_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';
    $username = trim($body['username'] ?? '');
    $firstName = trim($body['first_name'] ?? '');
    $middleName = trim($body['middle_name'] ?? '');
    $lastName = trim($body['last_name'] ?? '');
    $dateOfBirth = trim($body['date_of_birth'] ?? '');
    $gender = trim($body['gender'] ?? '');
    $civilStatus = trim($body['civil_status'] ?? '');
    $mobileNumber = trim($body['mobile_number'] ?? '');
    $address = trim($body['address'] ?? '');
    $status = $body['status'] ?? 'pending';

    if (!$email || !$password || !$firstName || !$lastName) {
        json_response(['error' => 'first_name, last_name, email, and password are required'], 422);
        exit;
    }
    if (!in_array($status, ['approved', 'rejected', 'pending'], true)) {
        json_response(['error' => 'Invalid status'], 422);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM residents WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['error' => 'Email already registered'], 409);
        exit;
    }

    if ($username !== '') {
        $stmt = $pdo->prepare('SELECT id FROM residents WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            json_response(['error' => 'Username already taken'], 409);
            exit;
        }
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $qrToken = generate_resident_qr_token($pdo);
    $stmt = $pdo->prepare(
        'INSERT INTO residents (username, first_name, middle_name, last_name, date_of_birth, gender, civil_status, mobile_number, address, email, password_hash, qr_token, status, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
    );
    $stmt->execute([
        $username !== '' ? $username : null,
        $firstName,
        $middleName !== '' ? $middleName : null,
        $lastName,
        $dateOfBirth !== '' ? $dateOfBirth : null,
        $gender !== '' ? $gender : null,
        $civilStatus !== '' ? $civilStatus : null,
        $mobileNumber !== '' ? $mobileNumber : null,
        $address !== '' ? $address : null,
        $email,
        $hash,
        $qrToken,
        $status,
    ]);
    $residentId = (int) $pdo->lastInsertId();

    log_audit($pdo, 'admin', (int) $payload['id'], 'resident.created', [
        'resident_id' => $residentId,
    ]);

    $stmt = $pdo->prepare('SELECT id, username, first_name, middle_name, last_name, date_of_birth, gender, civil_status, mobile_number, address, email, status, profile_photo_url, created_at FROM residents WHERE id = ?');
    $stmt->execute([$residentId]);
    json_response(['resident' => $stmt->fetch()], 201);
    exit;
}

if (preg_match('#^/api/admin/residents/(\\d+)$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $residentId = (int) $matches[1];
    $stmt = $pdo->prepare('SELECT id, email, username, qr_token FROM residents WHERE id = ?');
    $stmt->execute([$residentId]);
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    $body = read_json_body();
    $firstName = $body['first_name'] ?? null;
    $middleName = $body['middle_name'] ?? null;
    $lastName = $body['last_name'] ?? null;
    $email = $body['email'] ?? null;
    $username = $body['username'] ?? null;
    $dateOfBirth = $body['date_of_birth'] ?? null;
    $gender = $body['gender'] ?? null;
    $civilStatus = $body['civil_status'] ?? null;
    $mobileNumber = $body['mobile_number'] ?? null;
    $address = $body['address'] ?? null;
    $status = $body['status'] ?? null;
    $password = $body['password'] ?? null;

    if ($status !== null && !in_array($status, ['approved', 'rejected', 'pending'], true)) {
        json_response(['error' => 'Invalid status'], 422);
        exit;
    }

    if ($email !== null) {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            json_response(['error' => 'Email is required'], 422);
            exit;
        }
        if ($email !== $resident['email']) {
            $stmt = $pdo->prepare('SELECT id FROM residents WHERE email = ? AND id != ?');
            $stmt->execute([$email, $residentId]);
            if ($stmt->fetch()) {
                json_response(['error' => 'Email already registered'], 409);
                exit;
            }
        }
    }

    if ($username !== null) {
        $username = trim((string) $username);
        if ($username !== '' && $username !== $resident['username']) {
            $stmt = $pdo->prepare('SELECT id FROM residents WHERE username = ? AND id != ?');
            $stmt->execute([$username, $residentId]);
            if ($stmt->fetch()) {
                json_response(['error' => 'Username already taken'], 409);
                exit;
            }
        }
    }

    $updates = [];
    $params = [];
    if ($username !== null) {
        $updates[] = 'username = ?';
        $params[] = $username !== '' ? $username : null;
    }
    if ($firstName !== null) {
        $firstName = trim((string) $firstName);
        if ($firstName === '') {
            json_response(['error' => 'First name is required'], 422);
            exit;
        }
        $updates[] = 'first_name = ?';
        $params[] = $firstName;
    }
    if ($middleName !== null) {
        $middleName = trim((string) $middleName);
        $updates[] = 'middle_name = ?';
        $params[] = $middleName !== '' ? $middleName : null;
    }
    if ($lastName !== null) {
        $lastName = trim((string) $lastName);
        if ($lastName === '') {
            json_response(['error' => 'Last name is required'], 422);
            exit;
        }
        $updates[] = 'last_name = ?';
        $params[] = $lastName;
    }
    if ($dateOfBirth !== null) {
        $dateOfBirth = trim((string) $dateOfBirth);
        $updates[] = 'date_of_birth = ?';
        $params[] = $dateOfBirth !== '' ? $dateOfBirth : null;
    }
    if ($gender !== null) {
        $gender = trim((string) $gender);
        $updates[] = 'gender = ?';
        $params[] = $gender !== '' ? $gender : null;
    }
    if ($civilStatus !== null) {
        $civilStatus = trim((string) $civilStatus);
        $updates[] = 'civil_status = ?';
        $params[] = $civilStatus !== '' ? $civilStatus : null;
    }
    if ($mobileNumber !== null) {
        $mobileNumber = trim((string) $mobileNumber);
        $updates[] = 'mobile_number = ?';
        $params[] = $mobileNumber !== '' ? $mobileNumber : null;
    }
    if ($address !== null) {
        $address = trim((string) $address);
        $updates[] = 'address = ?';
        $params[] = $address !== '' ? $address : null;
    }
    if ($email !== null) {
        $updates[] = 'email = ?';
        $params[] = $email;
    }
    if ($status !== null) {
        $updates[] = 'status = ?';
        $params[] = $status;
    }
    if ($password !== null && $password !== '') {
        $updates[] = 'password_hash = ?';
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }
    if (!$updates) {
        json_response(['error' => 'No updates provided'], 422);
        exit;
    }

    $updates[] = 'updated_at = NOW()';
    $params[] = $residentId;
    $stmt = $pdo->prepare('UPDATE residents SET ' . implode(', ', $updates) . ' WHERE id = ?');
    $stmt->execute($params);

    if ($status === 'approved' && !$resident['qr_token']) {
        $qrToken = generate_resident_qr_token($pdo);
        $stmt = $pdo->prepare('UPDATE residents SET qr_token = ? WHERE id = ?');
        $stmt->execute([$qrToken, $residentId]);
    }
    if ($status !== null) {
        $stmt = $pdo->prepare('UPDATE resident_ids SET verification_status = ? WHERE resident_id = ?');
        $stmt->execute([$status, $residentId]);
    }

    log_audit($pdo, 'admin', (int) $payload['id'], 'resident.updated', [
        'resident_id' => $residentId,
    ]);

    $stmt = $pdo->prepare('SELECT r.id, r.username, r.first_name, r.middle_name, r.last_name, r.date_of_birth, r.gender,
                                  r.civil_status, r.mobile_number, r.address, r.email, r.status, r.profile_photo_url, r.created_at,
                                  ri.valid_id_url, ri.verification_status
                           FROM residents r
                           LEFT JOIN resident_ids ri ON ri.id = (
                             SELECT id FROM resident_ids WHERE resident_id = r.id ORDER BY created_at DESC LIMIT 1
                           )
                           WHERE r.id = ?');
    $stmt->execute([$residentId]);
    json_response(['resident' => $stmt->fetch()]);
    exit;
}

if (preg_match('#^/api/admin/residents/(\\d+)/delete$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $residentId = (int) $matches[1];
    $stmt = $pdo->prepare('SELECT id FROM residents WHERE id = ?');
    $stmt->execute([$residentId]);
    if (!$stmt->fetch()) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM queue_tickets WHERE resident_id = ?');
    $stmt->execute([$residentId]);
    if ((int) ($stmt->fetch()['count'] ?? 0) > 0) {
        json_response(['error' => 'Resident has queue history and cannot be deleted'], 409);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM resident_ids WHERE resident_id = ?');
    $stmt->execute([$residentId]);

    $stmt = $pdo->prepare('DELETE FROM residents WHERE id = ?');
    $stmt->execute([$residentId]);

    log_audit($pdo, 'admin', (int) $payload['id'], 'resident.deleted', [
        'resident_id' => $residentId,
    ]);

    json_response(['deleted' => true]);
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
    $stmt = $pdo->prepare('UPDATE resident_ids SET verification_status = ? WHERE resident_id = ?');
    $stmt->execute([$status, $residentId]);

    if ($status === 'approved') {
        $stmt = $pdo->prepare('SELECT qr_token FROM residents WHERE id = ?');
        $stmt->execute([$residentId]);
        $tokenRow = $stmt->fetch();
        if ($tokenRow && !$tokenRow['qr_token']) {
            $qrToken = generate_resident_qr_token($pdo);
            $stmt = $pdo->prepare('UPDATE residents SET qr_token = ? WHERE id = ?');
            $stmt->execute([$qrToken, $residentId]);
        }
    }

    log_audit($pdo, 'admin', (int) $payload['id'], 'resident.status.updated', [
        'resident_id' => $residentId,
        'status' => $status,
    ]);

    $stmt = $pdo->prepare('SELECT r.id, r.username, r.first_name, r.middle_name, r.last_name, r.date_of_birth, r.gender,
                                  r.civil_status, r.mobile_number, r.address, r.email, r.status, r.profile_photo_url, r.created_at,
                                  ri.valid_id_url, ri.verification_status
                           FROM residents r
                           LEFT JOIN resident_ids ri ON ri.id = (
                             SELECT id FROM resident_ids WHERE resident_id = r.id ORDER BY created_at DESC LIMIT 1
                           )
                           WHERE r.id = ?');
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
    $query = 'SELECT DISTINCT q.id, q.ticket_no, q.resident_id, q.service_id, q.status, q.issued_at,
                     r.username AS resident_username,
                     r.first_name AS resident_first_name,
                     r.middle_name AS resident_middle_name,
                     r.last_name AS resident_last_name,
                     r.email AS resident_email,
                     r.mobile_number AS resident_mobile_number,
                     r.address AS resident_address,
                     r.date_of_birth AS resident_date_of_birth,
                     r.gender AS resident_gender,
                     r.civil_status AS resident_civil_status
              FROM queue_tickets q
              JOIN residents r ON r.id = q.resident_id';
    $conditions = [];
    $params = [];

    if ($serviceId) {
        $query .= ' LEFT JOIN queue_ticket_services qts ON qts.ticket_id = q.id';
        $conditions[] = '(qts.service_id = ? OR q.service_id = ?)';
        $params[] = $serviceId;
        $params[] = $serviceId;
    }
    if ($status) {
        $conditions[] = 'q.status = ?';
        $params[] = $status;
    }
    if ($conditions) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY q.issued_at ASC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();
    $tickets = attach_services_to_tickets($pdo, $tickets);
    json_response(['tickets' => $tickets]);
    exit;
}

if ($path === '/api/admin/queue/next' && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $body = read_json_body();
    $serviceId = isset($body['service_id']) ? (int) $body['service_id'] : 0;
    if ($serviceId) {
        $stmt = $pdo->prepare(
            'SELECT q.id
             FROM queue_tickets q
             LEFT JOIN queue_ticket_services qts ON qts.ticket_id = q.id
             WHERE (qts.service_id = ? OR q.service_id = ?) AND q.status = ?
             ORDER BY q.issued_at ASC
             LIMIT 1'
        );
        $stmt->execute([$serviceId, $serviceId, 'waiting']);
    } else {
        $stmt = $pdo->prepare('SELECT id FROM queue_tickets WHERE status = ? ORDER BY issued_at ASC LIMIT 1');
        $stmt->execute(['waiting']);
    }
    $ticketRow = $stmt->fetch();
    if (!$ticketRow) {
        json_response(['error' => 'No waiting tickets'], 404);
        exit;
    }

    $ticketId = (int) $ticketRow['id'];
    $stmt = $pdo->prepare('UPDATE queue_tickets SET status = ? WHERE id = ?');
    $stmt->execute(['serving', $ticketId]);
    log_audit($pdo, 'admin', (int) $payload['id'], 'queue.serving', ['ticket_id' => $ticketId]);

    $ticket = fetch_queue_ticket($pdo, $ticketId);
    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        exit;
    }

    json_response(['ticket' => $ticket]);
    exit;
}

if (preg_match('#^/api/admin/queue/(\\d+)/call$#', $path, $matches) && $method === 'POST') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $ticketId = (int) $matches[1];
    $stmt = $pdo->prepare('SELECT id, status FROM queue_tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    $ticketRow = $stmt->fetch();
    if (!$ticketRow) {
        json_response(['error' => 'Ticket not found'], 404);
        exit;
    }
    if ($ticketRow['status'] !== 'waiting') {
        json_response(['error' => 'Only waiting tickets can be called'], 422);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE queue_tickets SET status = ? WHERE id = ?');
    $stmt->execute(['serving', $ticketId]);

    log_audit($pdo, 'admin', (int) $payload['id'], 'queue.called', [
        'ticket_id' => $ticketId,
    ]);

    $ticket = fetch_queue_ticket($pdo, $ticketId);
    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        exit;
    }

    json_response(['ticket' => $ticket]);
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

if (preg_match('#^/api/admin/queue/(\\d+)/print$#', $path, $matches) && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    ini_set('display_errors', '0');
    ini_set('html_errors', '0');

    try {
        $ticketId = (int) $matches[1];
        $ticket = fetch_queue_ticket($pdo, $ticketId);
        if (!$ticket) {
            json_response(['error' => 'Ticket not found'], 404);
            exit;
        }

        $serviceIds = [];
        foreach (($ticket['services'] ?? []) as $serviceItem) {
            $serviceId = (int) ($serviceItem['id'] ?? 0);
            if ($serviceId) {
                $serviceIds[] = $serviceId;
            }
        }
        if (!$serviceIds && !empty($ticket['service_id'])) {
            $serviceIds[] = (int) $ticket['service_id'];
        }
        $serviceIds = array_values(array_unique($serviceIds));
        $serviceLookup = fetch_services_by_ids($pdo, $serviceIds);
        $services = [];
        foreach ($serviceIds as $serviceId) {
            if (isset($serviceLookup[$serviceId])) {
                $services[] = $serviceLookup[$serviceId];
            }
        }
        if (!$services) {
            json_response(['error' => 'Service not found'], 404);
            exit;
        }

        $templateMap = [
            'PROOF_RESIDENCY' => 'template_residency.docx',
            'INDIGENCY' => 'template_indigency.docx',
            'INCOME_LOAN' => 'template_low_income.docx',
            'SOLO_PARENT' => 'template_solo_parent.docx',
            'SPECIAL_PERMIT' => 'template_special_permit.docx',
            'BUILDING_PERMIT' => 'template_building_permit.docx',
        ];
        $lastNameRaw = trim($ticket['resident_last_name'] ?? '');
        $lastName = $lastNameRaw !== '' ? ucwords(strtolower($lastNameRaw)) : '';
        $fullNameParts = [
            $ticket['resident_first_name'] ?? '',
            $ticket['resident_middle_name'] ?? '',
            $ticket['resident_last_name'] ?? '',
        ];
        $fullName = trim(preg_replace('/\\s+/', ' ', implode(' ', array_filter($fullNameParts))));
        $fullName = ucwords(strtolower($fullName));
        $address = trim($ticket['resident_address'] ?? '');
        $now = new DateTime('now');
        $month = $now->format('F');
        $day = $now->format('j');
        $date = $now->format('F j, Y');

        $age = '';
        if (!empty($ticket['resident_date_of_birth'])) {
            try {
                $dob = new DateTime($ticket['resident_date_of_birth']);
                $age = (string) $dob->diff($now)->y;
            } catch (Exception $e) {
                $age = '';
            }
        }

        $replacements = [
            '${FULL_NAME}' => $fullName,
            '${LAST_NAME}' => $lastName,
            '${ADDRESS}' => $address,
            '${AGE}' => $age,
            '${MONTH}' => $month,
            '${DAY}' => $day,
            '${DATE}' => $date,
        ];

        $safeTicket = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($ticket['ticket_no'] ?? $ticketId));
        $renderedFiles = [];
        foreach ($services as $service) {
            $templateFile = $templateMap[$service['code']] ?? null;
            if (!$templateFile) {
                json_response(['error' => 'Template not configured for this service'], 404);
                exit;
            }
            $templatePath = __DIR__ . '/../templates/' . $templateFile;
            $renderedPath = render_docx_template($templatePath, $replacements);
            if (!$renderedPath) {
                json_response(['error' => 'Unable to render template'], 500);
                exit;
            }
            $filename = sprintf('%s-%s.docx', strtolower($service['code']), $safeTicket ?: $ticketId);
            $renderedFiles[] = ['path' => $renderedPath, 'filename' => $filename];
        }

        if (count($renderedFiles) === 1) {
            $renderedPath = $renderedFiles[0]['path'];
            $filename = $renderedFiles[0]['filename'];
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($renderedPath));
            readfile($renderedPath);
            unlink($renderedPath);
            exit;
        }

        $mergedPath = merge_docx_documents(array_column($renderedFiles, 'path'));
        if (!$mergedPath) {
            foreach ($renderedFiles as $file) {
                @unlink($file['path']);
            }
            json_response(['error' => 'Unable to prepare templates'], 500);
            exit;
        }

        $mergedName = sprintf('ticket-%s-forms.docx', $safeTicket ?: $ticketId);
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $mergedName . '"');
        header('Content-Length: ' . filesize($mergedPath));
        readfile($mergedPath);

        foreach ($renderedFiles as $file) {
            @unlink($file['path']);
        }
        exit;
    } catch (Throwable $e) {
        json_response(['error' => 'Print failed: ' . $e->getMessage()], 500);
        exit;
    }
}

if ($path === '/api/admin/audit-logs' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $limit = (int) ($_GET['limit'] ?? 100);
    $limit = max(1, min($limit, 500));
    $stmt = $pdo->query(
        'SELECT id, actor_type, actor_id, action, meta_json, created_at FROM audit_logs ORDER BY created_at DESC LIMIT ' . $limit
    );
    $logs = $stmt->fetchAll();
    foreach ($logs as &$log) {
        $log['meta_json'] = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
    }
    json_response(['logs' => $logs]);
    exit;
}

if ($path === '/api/admin/analytics/timings' && $method === 'GET') {
    $payload = require_auth('admin', $appKey);
    if (!$payload) {
        exit;
    }

    $startParam = trim($_GET['start'] ?? '');
    $endParam = trim($_GET['end'] ?? '');
    $serviceId = (int) ($_GET['service_id'] ?? 0);

    $startDateTime = $startParam ? $startParam . ' 00:00:00' : null;
    $endDateTime = $endParam ? $endParam . ' 23:59:59' : null;

    $logConditions = [];
    $logParams = [];
    if ($startDateTime) {
        $logConditions[] = 'created_at >= ?';
        $logParams[] = $startDateTime;
    }
    if ($endDateTime) {
        $logConditions[] = 'created_at <= ?';
        $logParams[] = $endDateTime;
    }
    $logWhere = $logConditions ? (' AND ' . implode(' AND ', $logConditions)) : '';

    $ticketLogStmt = $pdo->prepare(
        'SELECT action, meta_json, created_at FROM audit_logs WHERE action = ?' . $logWhere . ' ORDER BY created_at ASC'
    );
    $ticketLogStmt->execute(array_merge(['kiosk.ticket.issued'], $logParams));
    $ticketLogs = $ticketLogStmt->fetchAll();

    $sessionTicketTimes = [];
    $sessionIds = [];
    foreach ($ticketLogs as $log) {
        $meta = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
        if (!$meta) {
            continue;
        }
        $sessionId = $meta['session_id'] ?? null;
        if (!$sessionId) {
            continue;
        }
        if ($serviceId && (int) ($meta['service_id'] ?? 0) !== $serviceId) {
            continue;
        }
        $sessionIds[$sessionId] = true;
        $sessionTicketTimes[$sessionId][] = strtotime($log['created_at']);
    }

    $sessionStarts = [];
    if (!empty($sessionIds)) {
        $sessionPlaceholders = implode(',', array_fill(0, count($sessionIds), '?'));
        $sessionStmt = $pdo->prepare(
            'SELECT meta_json, created_at FROM audit_logs WHERE action = ? AND JSON_UNQUOTE(JSON_EXTRACT(meta_json, "$.session_id")) IN (' .
                $sessionPlaceholders . ')'
        );
        $sessionStmt->execute(array_merge(['kiosk.session.start'], array_keys($sessionIds)));
        foreach ($sessionStmt->fetchAll() as $log) {
            $meta = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
            if (!$meta) {
                continue;
            }
            $sessionId = $meta['session_id'] ?? null;
            if (!$sessionId) {
                continue;
            }
            $sessionStarts[$sessionId] = strtotime($log['created_at']);
        }
    }

    $kioskDurations = [];
    $kioskSeriesBuckets = [];
    foreach ($sessionTicketTimes as $sessionId => $times) {
        $startTime = $sessionStarts[$sessionId] ?? null;
        if (!$startTime) {
            continue;
        }
        $endTime = max($times);
        if ($endTime > $startTime) {
            $durationMinutes = ($endTime - $startTime) / 60;
            $kioskDurations[] = $durationMinutes;
            $dateKey = date('Y-m-d', $endTime);
            if (!isset($kioskSeriesBuckets[$dateKey])) {
                $kioskSeriesBuckets[$dateKey] = ['sum' => 0, 'count' => 0];
            }
            $kioskSeriesBuckets[$dateKey]['sum'] += $durationMinutes;
            $kioskSeriesBuckets[$dateKey]['count'] += 1;
        }
    }

    $queueLogStmt = $pdo->prepare(
        'SELECT action, meta_json, created_at FROM audit_logs WHERE action IN (?, ?)' . $logWhere . ' ORDER BY created_at ASC'
    );
    $queueLogStmt->execute(array_merge(['queue.updated', 'queue.completed'], $logParams));
    $queueLogs = $queueLogStmt->fetchAll();

    $completedTickets = [];
    $completedTicketNos = [];
    foreach ($queueLogs as $log) {
        $meta = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
        if (!$meta) {
            continue;
        }
        if ($log['action'] === 'queue.updated') {
            if (($meta['status'] ?? '') !== 'done') {
                continue;
            }
            $ticketId = (int) ($meta['ticket_id'] ?? 0);
            if (!$ticketId) {
                continue;
            }
            if (!isset($completedTickets[$ticketId])) {
                $completedTickets[$ticketId] = strtotime($log['created_at']);
            }
        } elseif ($log['action'] === 'queue.completed') {
            $ticketNo = $meta['ticket_no'] ?? null;
            if (!$ticketNo) {
                continue;
            }
            if (!isset($completedTicketNos[$ticketNo])) {
                $completedTicketNos[$ticketNo] = strtotime($log['created_at']);
            }
        }
    }

    $ticketIds = array_keys($completedTickets);
    $ticketIssuedMap = [];
    if (!empty($ticketIds)) {
        $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));
        $ticketStmt = $pdo->prepare(
            'SELECT id, service_id, issued_at FROM queue_tickets WHERE id IN (' . $placeholders . ')'
        );
        $ticketStmt->execute($ticketIds);
        foreach ($ticketStmt->fetchAll() as $ticket) {
            if ($serviceId && (int) $ticket['service_id'] !== $serviceId) {
                continue;
            }
            $ticketIssuedMap[(int) $ticket['id']] = $ticket;
        }
    }

    if (!empty($completedTicketNos)) {
        $ticketNos = array_keys($completedTicketNos);
        $placeholders = implode(',', array_fill(0, count($ticketNos), '?'));
        $ticketStmt = $pdo->prepare(
            'SELECT id, ticket_no, service_id, issued_at FROM queue_tickets WHERE ticket_no IN (' . $placeholders . ')'
        );
        $ticketStmt->execute($ticketNos);
        foreach ($ticketStmt->fetchAll() as $ticket) {
            if ($serviceId && (int) $ticket['service_id'] !== $serviceId) {
                continue;
            }
            $ticketId = (int) $ticket['id'];
            if (!isset($completedTickets[$ticketId])) {
                $completedTickets[$ticketId] = $completedTicketNos[$ticket['ticket_no']] ?? null;
            }
            $ticketIssuedMap[$ticketId] = $ticket;
        }
    }

    $ticketDurations = [];
    $ticketSeriesBuckets = [];
    $waitSeriesBuckets = [];
    $serviceSeriesBuckets = [];
    foreach ($ticketIssuedMap as $id => $ticket) {
        $completedAt = $completedTickets[$id] ?? null;
        $issuedAt = isset($ticket['issued_at']) ? strtotime($ticket['issued_at']) : null;
        if ($completedAt && $issuedAt && $completedAt > $issuedAt) {
            $durationMinutes = ($completedAt - $issuedAt) / 60;
            $ticketDurations[] = $durationMinutes;
            $dateKey = date('Y-m-d', $issuedAt);
            if (!isset($ticketSeriesBuckets[$dateKey])) {
                $ticketSeriesBuckets[$dateKey] = ['sum' => 0, 'count' => 0];
            }
            $ticketSeriesBuckets[$dateKey]['sum'] += $durationMinutes;
            $ticketSeriesBuckets[$dateKey]['count'] += 1;
        }
    }

    $serveLogStmt = $pdo->prepare(
        'SELECT action, meta_json, created_at FROM audit_logs WHERE action IN (?, ?)' . $logWhere . ' ORDER BY created_at ASC'
    );
    $serveLogStmt->execute(array_merge(['queue.serving', 'queue.called'], $logParams));
    $serveLogs = $serveLogStmt->fetchAll();

    $serveTimes = [];
    foreach ($serveLogs as $log) {
        $meta = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
        if (!$meta) {
            continue;
        }
        $ticketId = (int) ($meta['ticket_id'] ?? 0);
        if (!$ticketId || !isset($ticketIssuedMap[$ticketId])) {
            continue;
        }
        if (!isset($serveTimes[$ticketId])) {
            $serveTimes[$ticketId] = strtotime($log['created_at']);
        }
    }

    $waitToServe = [];
    $serviceDurations = [];
    foreach ($ticketIssuedMap as $id => $ticket) {
        $issuedAt = isset($ticket['issued_at']) ? strtotime($ticket['issued_at']) : null;
        $serveAt = $serveTimes[$id] ?? null;
        $completedAt = $completedTickets[$id] ?? null;
        if ($issuedAt && $serveAt && $serveAt > $issuedAt) {
            $durationMinutes = ($serveAt - $issuedAt) / 60;
            $waitToServe[] = $durationMinutes;
            $dateKey = date('Y-m-d', $issuedAt);
            if (!isset($waitSeriesBuckets[$dateKey])) {
                $waitSeriesBuckets[$dateKey] = ['sum' => 0, 'count' => 0];
            }
            $waitSeriesBuckets[$dateKey]['sum'] += $durationMinutes;
            $waitSeriesBuckets[$dateKey]['count'] += 1;
        }
        if ($serveAt && $completedAt && $completedAt > $serveAt) {
            $durationMinutes = ($completedAt - $serveAt) / 60;
            $serviceDurations[] = $durationMinutes;
            $dateKey = date('Y-m-d', $serveAt);
            if (!isset($serviceSeriesBuckets[$dateKey])) {
                $serviceSeriesBuckets[$dateKey] = ['sum' => 0, 'count' => 0];
            }
            $serviceSeriesBuckets[$dateKey]['sum'] += $durationMinutes;
            $serviceSeriesBuckets[$dateKey]['count'] += 1;
        }
    }

    $buildSeries = static function (array $buckets): array {
        if (!$buckets) {
            return [];
        }
        ksort($buckets);
        $series = [];
        foreach ($buckets as $date => $bucket) {
            if (!$bucket['count']) {
                continue;
            }
            $series[] = [
                'date' => $date,
                'avg_minutes' => round($bucket['sum'] / $bucket['count'], 1),
                'count' => $bucket['count'],
            ];
        }
        return $series;
    };

    json_response([
        'kiosk' => compute_time_stats($kioskDurations),
        'ticket_cycle' => compute_time_stats($ticketDurations),
        'wait_to_serve' => compute_time_stats($waitToServe),
        'service_duration' => compute_time_stats($serviceDurations),
        'series' => [
            'kiosk_completion' => $buildSeries($kioskSeriesBuckets),
            'ticket_cycle' => $buildSeries($ticketSeriesBuckets),
            'wait_to_serve' => $buildSeries($waitSeriesBuckets),
            'service_duration' => $buildSeries($serviceSeriesBuckets),
        ],
    ]);
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
    $kioskDeviceId = (int) ($body['kiosk_device_id'] ?? 0);
    $qrToken = null;
    if (!$residentId && !empty($body['qr_code'])) {
        $parsed = parse_resident_qr_code((string) $body['qr_code']);
        $residentId = (int) ($parsed['resident_id'] ?? 0);
        $qrToken = $parsed['qr_token'] ?? null;
    }

    if (!$residentId && !$qrToken) {
        json_response(['error' => 'resident_id or qr_code is required'], 422);
        exit;
    }

    if ($qrToken && !$residentId) {
        $stmt = $pdo->prepare('SELECT id, first_name, last_name, status, qr_token FROM residents WHERE qr_token = ?');
        $stmt->execute([$qrToken]);
    } else {
        $stmt = $pdo->prepare('SELECT id, first_name, last_name, status, qr_token FROM residents WHERE id = ?');
        $stmt->execute([$residentId]);
    }
    $resident = $stmt->fetch();
    if (!$resident) {
        json_response(['error' => 'Resident not found'], 404);
        exit;
    }

    if ($qrToken && $resident['qr_token'] && strtoupper($resident['qr_token']) !== $qrToken) {
        json_response(['error' => 'Invalid QR code'], 401);
        exit;
    }

    $approved = $resident['status'] === 'approved';
    $services = [];
    if ($approved) {
        $stmt = $pdo->query('SELECT id, name, code FROM services WHERE active = 1 ORDER BY name');
        $services = $stmt->fetchAll();
    }

    $sessionId = bin2hex(random_bytes(8));
    log_audit($pdo, 'kiosk', $kioskDeviceId ?: 0, 'kiosk.session.start', [
        'session_id' => $sessionId,
        'resident_id' => (int) $resident['id'],
        'kiosk_device_id' => $kioskDeviceId ?: null,
        'approved' => $approved,
    ]);

    json_response([
        'approved' => $approved,
        'resident' => $resident,
        'allowed_services' => $services,
        'session_id' => $sessionId,
    ]);
    exit;
}

if ($path === '/api/kiosk/tickets' && $method === 'POST') {
    $body = read_json_body();
    $residentId = (int) ($body['resident_id'] ?? 0);
    $serviceId = (int) ($body['service_id'] ?? 0);
    $serviceIds = normalize_service_ids($body['service_ids'] ?? []);
    $kioskDeviceId = (int) ($body['kiosk_device_id'] ?? 0);
    $idempotencyKey = trim($body['idempotency_key'] ?? '');
    $sessionId = trim($body['session_id'] ?? '');

    if (!$serviceId && $serviceIds) {
        $serviceId = (int) $serviceIds[0];
    }
    if (!$serviceIds && $serviceId) {
        $serviceIds = [$serviceId];
    }

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

    if ($serviceIds) {
        $insertService = $pdo->prepare(
            'INSERT IGNORE INTO queue_ticket_services (ticket_id, service_id, created_at) VALUES (?, ?, NOW())'
        );
        foreach ($serviceIds as $serviceItemId) {
            $insertService->execute([$ticketId, (int) $serviceItemId]);
        }
    }

    $stmt = $pdo->prepare('SELECT * FROM queue_tickets WHERE id = ?');
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();

    if ($sessionId) {
        log_audit($pdo, 'kiosk', $kioskDeviceId ?: 0, 'kiosk.ticket.issued', [
            'session_id' => $sessionId,
            'ticket_id' => $ticketId,
            'service_id' => $serviceId,
            'service_ids' => $serviceIds,
            'resident_id' => $residentId,
            'kiosk_device_id' => $kioskDeviceId ?: null,
        ]);
    }

    json_response(['ticket' => $ticket], 201);
    exit;
}

if ($path === '/api/queue/current' && $method === 'GET') {
    $serviceId = (int) ($_GET['service_id'] ?? 0);
    $params = [];
    $nowServingQuery = 'SELECT ticket_no FROM queue_tickets WHERE status = "serving"';
    $query = 'SELECT ticket_no, service_id, status, issued_at FROM queue_tickets WHERE status = "waiting"';
    if ($serviceId) {
        $nowServingQuery .= ' AND service_id = ?';
        $query .= ' AND service_id = ?';
        $params[] = $serviceId;
    }
    $nowServingQuery .= ' ORDER BY issued_at DESC LIMIT 1';
    $query .= ' ORDER BY issued_at ASC LIMIT 5';

    $avgWaitMinutes = null;
    $logStmt = $pdo->prepare('SELECT meta_json, created_at, action FROM audit_logs WHERE DATE(created_at) = CURDATE() AND action IN (?, ?) ORDER BY created_at ASC');
    $logStmt->execute(['queue.updated', 'queue.completed']);
    $logs = $logStmt->fetchAll();

    $completedEntries = [];
    $ticketIds = [];
    $ticketNos = [];
    foreach ($logs as $log) {
        $meta = $log['meta_json'] ? json_decode($log['meta_json'], true) : null;
        if ($log['action'] === 'queue.updated') {
            if (($meta['status'] ?? '') !== 'done') {
                continue;
            }
            $ticketId = (int) ($meta['ticket_id'] ?? 0);
            if ($ticketId) {
                $ticketIds[] = $ticketId;
            }
            $completedEntries[] = [
                'completed_at' => $log['created_at'],
                'ticket_id' => $ticketId ?: null,
                'ticket_no' => null,
            ];
            continue;
        }
        if ($log['action'] === 'queue.completed') {
            $ticketNo = $meta['ticket_no'] ?? null;
            if ($ticketNo) {
                $ticketNos[] = $ticketNo;
            }
            $completedEntries[] = [
                'completed_at' => $log['created_at'],
                'ticket_id' => null,
                'ticket_no' => $ticketNo,
            ];
        }
    }

    if (!empty($completedEntries)) {
        $ticketsById = [];
        if (!empty($ticketIds)) {
            $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));
            $ticketStmt = $pdo->prepare('SELECT id, ticket_no, service_id, issued_at FROM queue_tickets WHERE id IN (' . $placeholders . ')');
            $ticketStmt->execute($ticketIds);
            foreach ($ticketStmt->fetchAll() as $ticket) {
                $ticketsById[(int) $ticket['id']] = $ticket;
            }
        }

        $ticketsByNo = [];
        if (!empty($ticketNos)) {
            $placeholders = implode(',', array_fill(0, count($ticketNos), '?'));
            $ticketStmt = $pdo->prepare('SELECT ticket_no, service_id, issued_at FROM queue_tickets WHERE ticket_no IN (' . $placeholders . ')');
            $ticketStmt->execute($ticketNos);
            foreach ($ticketStmt->fetchAll() as $ticket) {
                $ticketsByNo[$ticket['ticket_no']] = $ticket;
            }
        }

        $completionTimes = [];
        $fallbackDurationMinutes = null;
        foreach ($completedEntries as $entry) {
            $ticket = null;
            if ($entry['ticket_id']) {
                $ticket = $ticketsById[$entry['ticket_id']] ?? null;
            } elseif ($entry['ticket_no']) {
                $ticket = $ticketsByNo[$entry['ticket_no']] ?? null;
            }
            if (!$ticket) {
                continue;
            }
            if ($serviceId && (int) $ticket['service_id'] !== $serviceId) {
                continue;
            }
            $completionTimes[] = strtotime($entry['completed_at']);
            if ($fallbackDurationMinutes === null) {
                $issuedAt = strtotime($ticket['issued_at']);
                $completedAt = strtotime($entry['completed_at']);
                if ($issuedAt && $completedAt && $completedAt > $issuedAt) {
                    $fallbackDurationMinutes = ($completedAt - $issuedAt) / 60;
                }
            }
        }

        sort($completionTimes);
        $totalMinutes = 0;
        $countMinutes = 0;
        $maxGapMinutes = 30;
        for ($i = 1; $i < count($completionTimes); $i++) {
            $gapMinutes = ($completionTimes[$i] - $completionTimes[$i - 1]) / 60;
            if ($gapMinutes <= 0 || $gapMinutes > $maxGapMinutes) {
                continue;
            }
            $totalMinutes += $gapMinutes;
            $countMinutes += 1;
        }
        if ($countMinutes > 0) {
            $avgWaitMinutes = round($totalMinutes / $countMinutes, 1);
        } elseif ($fallbackDurationMinutes !== null) {
            $avgWaitMinutes = round($fallbackDurationMinutes, 1);
        }
    }

    $stmt = $pdo->prepare($nowServingQuery);
    $stmt->execute($params);
    $nowServing = $stmt->fetch();

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();

    json_response([
        'now_serving' => $nowServing ? $nowServing['ticket_no'] : null,
        'tickets' => $tickets,
        'avg_wait_minutes' => $avgWaitMinutes,
    ]);
    exit;
}

json_response(['error' => 'Not found'], 404);
