<?php
/**
 * Authentication & Authorization Middleware
 * Mengelola user session, role-based access, dan CSRF protection
 */

class Auth {
    /**
     * Check apakah user sudah login
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    /**
     * Get user dari session
     */
    public static function user() {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check user punya role tertentu
     */
    public static function hasRole($role) {
        $user = self::user();
        return $user && $user['role'] === $role;
    }

    /**
     * Check user punya salah satu dari roles
     */
    public static function hasAnyRole($roles) {
        $user = self::user();
        return $user && in_array($user['role'], (array)$roles);
    }

    /**
     * Set login session
     */
    public static function setLogin($userData) {
        $_SESSION['user'] = $userData;
        $_SESSION['user']['login_at'] = time();
        // Regenerate session ID untuk keamanan (hanya jika headers belum dikirim)
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
    }

    /**
     * Logout: hapus session
     */
    public static function logout() {
        // Clear session array
        $_SESSION = [];

        // Clear session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        // Regenerate session id for safety
        if (function_exists('session_regenerate_id')) {
            session_regenerate_id(true);
        }

        header('Location: ' . BASE_URL . 'index.php?action=auth.login');
        exit;
    }

    /**
     * Get role user yang login
     */
    public static function getRole() {
        $user = self::user();
        return $user['role'] ?? null;
    }

    /**
     * Get ID user yang login
     */
    public static function userId() {
        $user = self::user();
        return $user['id_user'] ?? null;
    }
}

// Simple login throttling stored in session (per-email or per-ip)
class LoginThrottle {
    // max attempts before block
    private static $maxAttempts = 5;
    // block duration in seconds
    private static $blockSeconds = 300; // 5 minutes

    public static function keyFor($emailOrIp) {
        return 'login_attempts_' . md5(strtolower($emailOrIp));
    }

    public static function recordAttempt($key) {
        $now = time();
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'first' => $now, 'blocked_until' => 0];
        }
        $_SESSION[$key]['count']++;
        // if exceed max, set block
        if ($_SESSION[$key]['count'] >= self::$maxAttempts) {
            $_SESSION[$key]['blocked_until'] = $now + self::$blockSeconds;
        }
    }

    public static function isBlocked($key) {
        if (!isset($_SESSION[$key])) return false;
        $b = $_SESSION[$key]['blocked_until'] ?? 0;
        if ($b === 0) return false;
        if (time() > $b) {
            // unblock and reset
            unset($_SESSION[$key]);
            return false;
        }
        return true;
    }

    public static function clear($key) {
        unset($_SESSION[$key]);
    }
}

/**
 * Role-Based Guard Middleware
 */
class Guard {
    /**
     * Redirect jika user tidak login
     */
    public static function requireLogin() {
        if (!Auth::isLoggedIn()) {
            $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            exit;
        }
    }

    /**
     * Redirect jika user bukan role tertentu
     */
    public static function requireRole($role) {
        self::requireLogin();
        if (!Auth::hasRole($role)) {
            http_response_code(403);
            die('Access Denied: You do not have permission to access this page.');
        }
    }

    /**
     * Redirect jika user bukan salah satu dari roles
     */
    public static function requireAnyRole($roles) {
        self::requireLogin();
        if (!Auth::hasAnyRole($roles)) {
            http_response_code(403);
            die('Access Denied: You do not have permission to access this page.');
        }
    }
}

/**
 * CSRF Token Helper
 */
class Csrf {
    /**
     * Verify CSRF token
     */
    public static function verify($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get CSRF token
     */
    public static function token() {
        return $_SESSION['csrf_token'] ?? null;
    }

    /**
     * Generate hidden input CSRF
     */
    public static function field() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token()) . '">';
    }
}
