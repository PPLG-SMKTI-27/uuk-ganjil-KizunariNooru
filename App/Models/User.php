<?php
/**
 * User Model
 * Mengelola operasi user dengan keamanan tinggi (prepared statements, bcrypt)
 */

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Helpers/Validator.php';
require_once __DIR__ . '/../Helpers/Sanitizer.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find user by ID
     */
    public function find($id) {
        $sql = "SELECT id_user, email, role FROM user WHERE id_user = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id], 'i');
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $sql = "SELECT id_user, email, password, role FROM user WHERE email = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$email], 's');
    }

    /**
     * Verify login credentials
     */
    public function verifyLogin($email, $password) {
        if (!Validator::email($email)) {
            return null;
        }

        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }

        if (!Sanitizer::verifyPassword($password, $user['password'])) {
            return null;
        }

        // Return user tanpa password
        unset($user['password']);
        return $user;
    }

    /**
     * Get all users (untuk admin)
     */
    public function getAll() {
        $sql = "SELECT u.id_user, u.email, u.role FROM user u ORDER BY u.id_user DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get users by role
     */
    public function getByRole($role) {
        $sql = "SELECT id_user, email, role FROM user WHERE role = ? ORDER BY id_user DESC";
        return $this->db->fetchAll($sql, [$role], 's');
    }

    /**
     * Count users by role
     */
    public function countByRole($role = null) {
        if ($role === null) {
            // Count all users
            $sql = "SELECT COUNT(*) as total FROM user";
            $result = $this->db->fetchOne($sql);
        } else {
            // Count by specific role
            $sql = "SELECT COUNT(*) as total FROM user WHERE role = ?";
            $result = $this->db->fetchOne($sql, [$role], 's');
        }
        return (int)($result['total'] ?? 0);
    }

    /**
     * Create new user
     */
    public function create($email, $password, $role) {
        // Validasi
        if (!Validator::email($email)) {
            Validator::addError('email', 'Format email tidak valid');
            return false;
        }

        if (strlen($password) < 6) {
            Validator::addError('password', 'Password minimal 6 karakter');
            return false;
        }

        // Cek email sudah ada
        if ($this->findByEmail($email)) {
            Validator::addError('email', 'Email sudah terdaftar');
            return false;
        }

        // Hash password dengan bcrypt
        $hashedPassword = Sanitizer::password($password);

        // Insert ke database
        $sql = "INSERT INTO user (email, password, role) VALUES (?, ?, ?)";
        $this->db->query($sql, [$email, $hashedPassword, $role], 'sss');

        if ($this->db->affectedRows() > 0) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update user
     */
    public function update($id, $email = null, $password = null, $role = null) {
        $updates = [];
        $params = [];
        $types = '';

        if ($email !== null) {
            // Cek email belum digunakan user lain
            $existing = $this->db->fetchOne(
                "SELECT id_user FROM user WHERE email = ? AND id_user != ?",
                [$email, (int)$id],
                'si'
            );

            if ($existing) {
                Validator::addError('email', 'Email sudah digunakan user lain');
                return false;
            }

            $updates[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }

        if ($password !== null && strlen($password) >= 6) {
            $hashedPassword = Sanitizer::password($password);
            $updates[] = "password = ?";
            $params[] = $hashedPassword;
            $types .= 's';
        }

        if ($role !== null) {
            $updates[] = "role = ?";
            $params[] = $role;
            $types .= 's';
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = (int)$id;
        $types .= 'i';

        $sql = "UPDATE user SET " . implode(', ', $updates) . " WHERE id_user = ?";
        $this->db->query($sql, $params, $types);

        return $this->db->affectedRows() > 0;
    }

    /**
     * Delete user
     */
    public function delete($id) {
        $sql = "DELETE FROM user WHERE id_user = ?";
        $this->db->query($sql, [(int)$id], 'i');
        return $this->db->affectedRows() > 0;
    }

    /**
     * Update password
     */
    public function updatePassword($id, $newPassword) {
        if (strlen($newPassword) < 6) {
            Validator::addError('password', 'Password minimal 6 karakter');
            return false;
        }

        $hashedPassword = Sanitizer::password($newPassword);
        return $this->update((int)$id, null, $newPassword, null);
    }
}
?>
