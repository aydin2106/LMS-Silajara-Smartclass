<?php
require __DIR__ . '/config/bootstrap.php';
$pdo = Database::connection();
$action = trim((string)($_GET['page'] ?? ''));
if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $ok = Auth::attempt(trim($_POST['username'] ?? ''), (string)($_POST['password'] ?? ''));
        if ($ok) redirect('dashboard');
        flash('error', 'Username/NISN atau password tidak sesuai.');
        redirect('login');
    }
    require __DIR__ . '/views/auth/login.php'; exit;
}
if ($action === 'logout') { Auth::requireLogin(); Auth::logout(); redirect('login'); }
if ($action === 'api') { require __DIR__ . '/app/ApiController.php'; exit; }
if ($action === '' || $action === 'home') {
    if (Auth::check()) redirect('dashboard');
    require __DIR__ . '/views/public/home.php'; exit;
}
if ($action === 'dashboard') {
    Auth::requireLogin();
    $role = current_user()['role'];
    if ($role === 'admin') require __DIR__ . '/views/admin/dashboard.php';
    elseif ($role === 'guru') require __DIR__ . '/views/guru/dashboard.php';
    else require __DIR__ . '/views/siswa/dashboard.php';
    exit;
}
Auth::requireLogin();
$role = current_user()['role'];
$allowed = [
 'admin' => ['users','classes','subjects','assignments','reports'],
 'guru' => ['modules','materials','tasks','submissions','attendance'],
 'siswa' => ['learning','tasks','grades','attendance']
];
$page = $action ?: 'dashboard';
if (!in_array($page, $allowed[$role] ?? [], true)) { http_response_code(403); exit('403 - Anda tidak memiliki akses ke halaman ini.'); }
$file = __DIR__ . "/views/{$role}/{$page}.php";
if (!is_file($file)) { http_response_code(404); exit('Halaman tidak ditemukan.'); }
require $file;
