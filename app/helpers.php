<?php
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void {
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419); exit('Sesi formulir kedaluwarsa. Muat ulang halaman.');
    }
}
function flash(string $type, string $message): void { $_SESSION['_flash'] = compact('type','message'); }
function get_flash(): ?array { $f = $_SESSION['_flash'] ?? null; unset($_SESSION['_flash']); return $f; }
function json_response(array $data, int $status = 200): never {
    http_response_code($status); header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE); exit;
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name)); $out = '';
    foreach (array_slice($parts ?: [], 0, 2) as $p) $out .= strtoupper(substr($p,0,1));
    return $out ?: 'U';
}
function format_date(?string $date): string { return $date ? date('d M Y', strtotime($date)) : '-'; }
