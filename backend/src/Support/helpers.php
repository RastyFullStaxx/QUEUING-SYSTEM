<?php

namespace App\Support;

function load_env(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    $vars = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");
        if ($key !== '') {
            $vars[$key] = $value;
        }
    }
    return $vars;
}

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
}

function read_json_body(): array
{
    $input = file_get_contents('php://input');
    if (!$input) {
        return [];
    }
    $decoded = json_decode($input, true);
    return is_array($decoded) ? $decoded : [];
}

function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string
{
    $data = strtr($data, '-_', '+/');
    $padding = strlen($data) % 4;
    if ($padding) {
        $data .= str_repeat('=', 4 - $padding);
    }
    return base64_decode($data);
}

function issue_token(string $type, int $id, string $appKey): string
{
    $payload = json_encode([
        'type' => $type,
        'id' => $id,
        'iat' => time(),
    ]);
    $encoded = base64url_encode($payload);
    $sig = hash_hmac('sha256', $encoded, $appKey);
    return $encoded . '.' . $sig;
}

function verify_token(?string $token, string $appKey): ?array
{
    if (!$token || !str_contains($token, '.')) {
        return null;
    }
    [$encoded, $sig] = explode('.', $token, 2);
    $expected = hash_hmac('sha256', $encoded, $appKey);
    if (!hash_equals($expected, $sig)) {
        return null;
    }
    $payload = json_decode(base64url_decode($encoded), true);
    return is_array($payload) ? $payload : null;
}
