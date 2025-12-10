<?php
/**
 * Auth Controller
 * Mengelola login, logout, dan session management
 */

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Middleware/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';
require_once __DIR__ . '/../Helpers/Sanitizer.php';

class AuthController {
    /**
     * Show login form
     */
    public function login() {
        // Jika sudah login, redirect ke dashboard
        if (Auth::isLoggedIn()) {
            $this->redirectByRole();
        }

        $view = __DIR__ . '/../Views/Auth/login.php';
        include $view;
    }

    /**
     * Process login
     */
    public function processLogin() {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !Csrf::verify($_POST['csrf_token'])) {
            $_SESSION['error'] = 'Invalid CSRF token';
            header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            exit;
        }

        // Check method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            exit;
        }

        // Get & sanitize input
        $rawEmail = isset($_POST['email']) ? trim($_POST['email']) : '';
        // allow login by email or NIS (numeric), decide key for throttling
        $email = Sanitizer::email($rawEmail);
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Throttle key: prefer email if looks like email, else use IP
        $throttleKey = filter_var($rawEmail, FILTER_VALIDATE_EMAIL) ? $rawEmail : ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $throttleSessionKey = LoginThrottle::keyFor($throttleKey);

        // Check throttle
        if (LoginThrottle::isBlocked($throttleSessionKey)) {
            $_SESSION['login_errors'] = ['email' => 'Terlalu banyak percobaan login. Coba lagi nanti.'];
            header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            exit;
        }
        $errors = [];

        // Validate input
        if (!Validator::required($email)) {
            $errors['email'] = 'Email harus diisi';
        } elseif (!Validator::email($email)) {
            $errors['email'] = 'Format email tidak valid';
        }

        if (!Validator::required($password)) {
            $errors['password'] = 'Password harus diisi';
        }

        // Jika ada error, return ke login
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_email'] = $email;
            header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            exit;
        }

        // Verify credentials
        $userModel = new User();
        $user = $userModel->verifyLogin($email, $password);

        if ($user) {
            // Set session
            Auth::setLogin($user);

            // Clear flash and throttle
            unset($_SESSION['login_errors']);
            unset($_SESSION['login_email']);
            LoginThrottle::clear($throttleSessionKey);

            // Redirect by role
            $this->redirectByRole();
        } else {
            // Record failed attempt
            LoginThrottle::recordAttempt($throttleSessionKey);

            // Login gagal
            $_SESSION['login_errors'] = ['email' => 'Email atau password salah'];
            $_SESSION['login_email'] = htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8');
            header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            exit;
        }
    }

    /**
     * Logout
     */
    public function logout() {
        Auth::logout();
    }

    /**
     * Helper: redirect berdasarkan role
     */
    private function redirectByRole() {
        $user = Auth::user();
        $redirectUrl = $_SESSION['redirect_to'] ?? null;

        if ($redirectUrl) {
            unset($_SESSION['redirect_to']);
            header('Location: ' . $redirectUrl);
        } else {
            switch ($user['role']) {
                case 'Siswa':
                    header('Location: ' . BASE_URL . 'index.php?action=siswa.dashboard');
                    break;
                case 'WaliKelas':
                    header('Location: ' . BASE_URL . 'index.php?action=wali.index');
                    break;
                case 'Admin':
                    header('Location: ' . BASE_URL . 'index.php?action=admin.index');
                    break;
                default:
                    header('Location: ' . BASE_URL . 'index.php?action=auth.login');
            }
        }
        exit;
    }
}
