<?php
/**
 * Siswa Model
 * Mengelola data siswa dengan validasi & keamanan
 */

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class Siswa {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find siswa by user ID
     */
    public function findByUserId($id_user) {
        $sql = "SELECT * FROM siswa WHERE id_user = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id_user], 'i');
    }

    /**
     * Find siswa by siswa ID
     */
    public function findById($id_siswa) {
        $sql = "SELECT * FROM siswa WHERE id_siswa = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id_siswa], 'i');
    }

    /**
     * Get all siswa (untuk admin)
     */
    public function getAll() {
        $sql = "SELECT s.*, u.email, p.nama as nama_wali FROM siswa s 
                LEFT JOIN user u ON s.id_user = u.id_user 
                LEFT JOIN pegawai p ON s.id_walikelas = p.id_pegawai 
                ORDER BY s.id_siswa DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get siswa by kelas
     */
    public function getByKelas($kelas) {
        $sql = "SELECT s.*, u.email FROM siswa s 
                LEFT JOIN user u ON s.id_user = u.id_user 
                WHERE s.kelas = ? 
                ORDER BY s.nama_siswa ASC";
        return $this->db->fetchAll($sql, [$kelas], 's');
    }

    /**
     * Create siswa
     */
    public function create($id_user, $nama_siswa, $nisn, $kelas, $alamat) {
        // Validasi
        if (!Validator::required($nama_siswa)) {
            Validator::addError('nama_siswa', 'Nama siswa harus diisi');
            return false;
        }

        if (!Validator::nisn($nisn)) {
            Validator::addError('nisn', 'NISN harus 10 digit');
            return false;
        }

        if (!Validator::required($kelas)) {
            Validator::addError('kelas', 'Kelas harus diisi');
            return false;
        }

        // Cek NISN belum terdaftar
        $existing = $this->db->fetchOne(
            "SELECT id_siswa FROM siswa WHERE nisn = ?",
            [$nisn],
            's'
        );
        if ($existing) {
            Validator::addError('nisn', 'NISN sudah terdaftar');
            return false;
        }

        // Insert
        $sql = "INSERT INTO siswa (id_user, nama_siswa, nisn, kelas, alamat) VALUES (?, ?, ?, ?, ?)";
        $this->db->query($sql, [(int)$id_user, $nama_siswa, $nisn, $kelas, $alamat], 'issss');

        return $this->db->affectedRows() > 0 ? $this->db->lastInsertId() : false;
    }

    /**
     * Update siswa
     */
    public function update($id_siswa, $nama_siswa = null, $nisn = null, $kelas = null, $alamat = null) {
        $updates = [];
        $params = [];
        $types = '';

        if ($nama_siswa !== null) {
            if (!Validator::required($nama_siswa)) {
                Validator::addError('nama_siswa', 'Nama siswa harus diisi');
                return false;
            }
            $updates[] = "nama_siswa = ?";
            $params[] = $nama_siswa;
            $types .= 's';
        }

        if ($nisn !== null) {
            if (!Validator::nisn($nisn)) {
                Validator::addError('nisn', 'NISN harus 10 digit');
                return false;
            }

            // Cek NISN belum digunakan siswa lain
            $existing = $this->db->fetchOne(
                "SELECT id_siswa FROM siswa WHERE nisn = ? AND id_siswa != ?",
                [$nisn, (int)$id_siswa],
                'si'
            );
            if ($existing) {
                Validator::addError('nisn', 'NISN sudah terdaftar');
                return false;
            }

            $updates[] = "nisn = ?";
            $params[] = $nisn;
            $types .= 's';
        }

        if ($kelas !== null) {
            $updates[] = "kelas = ?";
            $params[] = $kelas;
            $types .= 's';
        }

        if ($alamat !== null) {
            $updates[] = "alamat = ?";
            $params[] = $alamat;
            $types .= 's';
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = (int)$id_siswa;
        $types .= 'i';

        $sql = "UPDATE siswa SET " . implode(', ', $updates) . " WHERE id_siswa = ?";
        $this->db->query($sql, $params, $types);

        return $this->db->affectedRows() > 0;
    }

    /**
     * Delete siswa by user ID
     */
    public function deleteByUserId($id_user) {
        $sql = "DELETE FROM siswa WHERE id_user = ?";
        $this->db->query($sql, [(int)$id_user], 'i');
        return $this->db->affectedRows() > 0;
    }

    /**
     * Delete siswa by siswa ID
     */
    public function deleteById($id_siswa) {
        $sql = "DELETE FROM siswa WHERE id_siswa = ?";
        $this->db->query($sql, [(int)$id_siswa], 'i');
        return $this->db->affectedRows() > 0;
    }

    /**
     * Count total siswa
     */
    public function countTotal() {
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM siswa");
        return $result['total'] ?? 0;
    }
}
?>
