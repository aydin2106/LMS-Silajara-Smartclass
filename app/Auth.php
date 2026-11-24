<?php
final class Auth {
    public static function attempt(string $username, string $password): bool {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$username]); $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) return false;
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        return true;
    }
    public static function check(): bool { return current_user() !== null; }
    public static function requireLogin(): void { if (!self::check()) redirect('login'); }
    public static function requireRole(array $roles): void {
        self::requireLogin();
        if (!in_array(current_user()['role'], $roles, true)) { http_response_code(403); exit('403 - Akses ditolak.'); }
    }
    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
