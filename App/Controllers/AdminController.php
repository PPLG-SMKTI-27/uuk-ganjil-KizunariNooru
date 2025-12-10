<?php
/**
 * AdminController - Admin Dashboard & User Management
 * 
 * Mengelola semua user (Siswa, Guru, Admin)
 * CRUD operations untuk user, siswa, dan guru
 * 
 * Requirements:
 * - Guard::requireRole('Admin') pada semua method
 * - CSRF verification pada POST requests
 * - Audit logging untuk sensitive operations
 */

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Siswa.php';
require_once __DIR__ . '/../Middleware/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class AdminController {

    /**
     * Admin Dashboard - List all users
     * 
     * @access Admin
     */
    public function dashboard() {
        Guard::requireRole('Admin');
        
        $userModel = new User();
        
        // Get stats
        $totalUsers = $userModel->countByRole(null); // null = semua role
        $totalSiswa = $userModel->countByRole('Siswa');
        $totalGuru = $userModel->countByRole('WaliKelas');
        $totalAdmin = $userModel->countByRole('Admin');
        
        // Get recent users
        $users = $userModel->getAll();
        
        include __DIR__ . '/../Views/admin/dashboard.php';
    }

    /**
     * List all users with filter & search
     * 
     * @access Admin
     */
    public function index() {
        Guard::requireRole('Admin');
        
        $userModel = new User();
        
        // Filter by role
        $roleFilter = $_GET['role'] ?? null;
        $searchTerm = $_GET['search'] ?? '';
        
        // Get users berdasarkan filter
        if ($roleFilter) {
            $users = $userModel->getByRole($roleFilter);
        } else {
            $users = $userModel->getAll();
        }
        
        // Filter by search term
        if (!empty($searchTerm)) {
            $users = array_filter($users, function($user) use ($searchTerm) {
                return stripos($user['email'], $searchTerm) !== false ||
                       stripos($user['name'] ?? '', $searchTerm) !== false;
            });
        }
        
        include __DIR__ . '/../Views/admin/index.php';
    }

    /**
     * Show create user form
     * 
     * @access Admin
     */
    public function create() {
        Guard::requireRole('Admin');
        
        include __DIR__ . '/../Views/admin/create_user.php';
    }

    /**
     * Store new user with validation
     * 
     * @access Admin
     * @post email, password, role, [nama_siswa, nisn, nik, kelas, alamat]
     */
    public function store() {
        Guard::requireRole('Admin');
        
        // CSRF verification
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid CSRF token';
            header('Location: ' . BASE_URL . 'index.php?action=admin.create');
            exit;
        }
        
        // Sanitize input
        $email = Sanitizer::email($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'Siswa';
        
        // Validate
        if (!Validator::email($email)) {
            Validator::addError('email', 'Email tidak valid');
        }

        if (empty($password) || strlen($password) < 6) {
            Validator::addError('password', 'Password minimal 6 karakter');
        }

        if (!in_array($role, ['Siswa', 'WaliKelas', 'Admin'])) {
            Validator::addError('role', 'Role tidak valid');
        }

        // Check if email already exists
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            Validator::addError('email', 'Email sudah terdaftar');
        }

        // If validation fails
        if (Validator::hasError()) {
            $_SESSION['errors'] = Validator::errors();
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . 'index.php?action=admin.create');
            exit;
        }
        
        // Create user
        $hashedPassword = Sanitizer::password($password);
        $userId = $userModel->create($email, $hashedPassword, $role);
        
        // If Siswa, create siswa record
        if ($userId && $role === 'Siswa') {
            $namaSiswa = $_POST['nama_siswa'] ?? '';
            $nisn = $_POST['nisn'] ?? '';
            $kelas = $_POST['kelas'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            
            // Validate NISN
            if (!Validator::nisn($nisn)) {
                $_SESSION['error'] = 'NISN tidak valid (10 digit)';
                // Delete user yang baru dibuat
                $userModel->delete($userId);
                header('Location: ' . BASE_URL . 'index.php?action=admin.create');
                exit;
            }
            
            $siswaModel = new Siswa();
            $siswaModel->create($userId, $namaSiswa, $nisn, $kelas, $alamat);
        }
        
        $_SESSION['success'] = 'User berhasil dibuat';
        header('Location: ' . BASE_URL . 'index.php?action=admin.index');
        exit;
    }

    /**
     * Show edit user form
     * 
     * @access Admin
     * @param id User ID
     */
    public function edit() {
        Guard::requireRole('Admin');
        
        $userId = (int)($_GET['id'] ?? 0);
        if (!$userId) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        // Get siswa data if role is Siswa
        $siswa = null;
        if ($user['role'] === 'Siswa') {
            $siswaModel = new Siswa();
            $siswa = $siswaModel->findByUserId($userId);
        }
        
        include __DIR__ . '/../Views/admin/edit_user.php';
    }

    /**
     * Update user data
     * 
     * @access Admin
     * @post email, role, password, [nama_siswa, kelas]
     */
    public function update() {
        Guard::requireRole('Admin');
        
        // CSRF verification
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid CSRF token';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        $userId = (int)($_POST['id'] ?? 0);
        if (!$userId) {
            $_SESSION['error'] = 'User tidak valid';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        // Sanitize input
        $email = Sanitizer::email($_POST['email'] ?? '');
        $role = $_POST['role'] ?? $user['role'];
        $password = $_POST['password'] ?? '';
        
        // Validate
        if (!Validator::email($email)) {
            Validator::addError('email', 'Email tidak valid');
        }

        if (!empty($password) && strlen($password) < 6) {
            Validator::addError('password', 'Password minimal 6 karakter');
        }

        // Check if new email already exists (and is different from current)
        if ($email !== $user['email'] && $userModel->findByEmail($email)) {
            Validator::addError('email', 'Email sudah digunakan');
        }

        if (Validator::hasError()) {
            $_SESSION['errors'] = Validator::errors();
            header('Location: ' . BASE_URL . 'index.php?action=admin.edit&id=' . $userId);
            exit;
        }
        
        // Update user - pass null untuk fields yang tidak berubah
        $userModel->update(
            $userId,
            $email,                              // email
            !empty($password) ? $password : null, // password
            $role                                  // role
        );
        
        // Update siswa if role is Siswa
        if ($role === 'Siswa') {
            $namaSiswa = $_POST['nama_siswa'] ?? '';
            $kelas = $_POST['kelas'] ?? '';
            $alamat = $_POST['alamat'] ?? '';

            $siswaModel = new Siswa();
            $siswaData = $siswaModel->findByUserId($userId);

            if ($siswaData) {
                // Update existing
                $siswaModel->update($siswaData['id_siswa'], $namaSiswa, null, $kelas, $alamat);
            } else {
                // Create new if doesn't exist
                $nisn = $_POST['nisn'] ?? '';
                if (Validator::nisn($nisn)) {
                    $siswaModel->create($userId, $namaSiswa, $nisn, $kelas, $alamat);
                }
            }
        }
        
        $_SESSION['success'] = 'User berhasil diupdate';
        header('Location: ' . BASE_URL . 'index.php?action=admin.index');
        exit;
    }

    /**
     * Delete user and related data
     * 
     * @access Admin
     * @param id User ID
     */
    public function delete() {
        Guard::requireRole('Admin');
        
        $userId = (int)($_GET['id'] ?? 0);
        if (!$userId) {
            $_SESSION['error'] = 'User tidak valid';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        // Prevent deleting own account
        if ($userId === Auth::userId()) {
            $_SESSION['error'] = 'Tidak bisa menghapus akun sendiri';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: ' . BASE_URL . 'index.php?action=admin.index');
            exit;
        }
        
        // Delete related siswa data
        if ($user['role'] === 'Siswa') {
            $siswaModel = new Siswa();
            $siswaData = $siswaModel->findByUserId($userId);
            
            if ($siswaData) {
                // Delete izin records
                require_once __DIR__ . '/../Models/Izin.php';
                $izinModel = new Izin();
                $izinModel->deleteBySiswa($siswaData['id_siswa']);
                
                // Delete siswa record
                $siswaModel->delete($siswaData['id_siswa']);
            }
        }
        
        // Delete user
        $userModel->delete($userId);
        
        $_SESSION['success'] = 'User dan data terkait berhasil dihapus';
        header('Location: ' . BASE_URL . 'index.php?action=admin.index');
        exit;
    }

    /**
     * Export users to CSV
     * 
     * @access Admin
     */
    public function export() {
        Guard::requireRole('Admin');
        
        $userModel = new User();
        $users = $userModel->getAll();
        
        // Set headers for CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users_' . date('Y-m-d_H-i-s') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['ID', 'Email', 'Role', 'Created At']);
        
        // Write data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id_user'],
                $user['email'],
                $user['role'],
                $user['created_at'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Get user statistics
     * 
     * @access Admin
     */
    public function stats() {
        Guard::requireRole('Admin');
        
        $userModel = new User();
        
        $stats = [
            'total_users' => $userModel->countByRole(null),
            'total_siswa' => $userModel->countByRole('Siswa'),
            'total_guru' => $userModel->countByRole('WaliKelas'),
            'total_admin' => $userModel->countByRole('Admin'),
        ];
        
        // Return as JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
}

