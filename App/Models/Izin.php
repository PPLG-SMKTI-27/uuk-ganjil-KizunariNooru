<?php
/**
 * Izin Model
 * Mengelola data izin siswa dengan prepared statements & validasi
 */

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class Izin {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get izin by ID
     */
    public function findById($id_izin) {
        $sql = "SELECT * FROM tb_izin WHERE id_izin = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id_izin], 'i');
    }

    /**
     * Get all izin by siswa
     */
    public function getBySiswa($id_siswa) {
        $sql = "SELECT i.*, s.nama_siswa, s.kelas, s.nisn 
                FROM tb_izin i 
                JOIN siswa s ON i.id_siswa = s.id_siswa 
                WHERE i.id_siswa = ? 
                ORDER BY i.rencana_keluar DESC";
        return $this->db->fetchAll($sql, [(int)$id_siswa], 'i');
    }

    /**
     * Get all izin (untuk guru/admin)
     */
    public function getAll($status = null, $kelas = null) {
        $sql = "SELECT i.*, s.nama_siswa, s.kelas, p.nama as nama_wali 
                FROM tb_izin i 
                JOIN siswa s ON i.id_siswa = s.id_siswa 
                LEFT JOIN pegawai p ON i.id_approve = p.id_pegawai 
                WHERE 1=1";
        
        $params = [];
        $types = '';

        if ($status) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        if ($kelas) {
            $sql .= " AND s.kelas = ?";
            $params[] = $kelas;
            $types .= 's';
        }

        $sql .= " ORDER BY i.rencana_keluar DESC";

        return $this->db->fetchAll($sql, $params, $types);
    }

    /**
     * Get izin untuk wali kelas tertentu
     */
    public function getForWali($id_wali, $status = null) {
        $sql = "SELECT i.*, s.nama_siswa, s.kelas, s.nisn 
                FROM tb_izin i 
                JOIN siswa s ON i.id_siswa = s.id_siswa 
                WHERE s.id_walikelas = ?";
        
        $params = [(int)$id_wali];
        $types = 'i';

        if ($status) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        $sql .= " ORDER BY i.rencana_keluar DESC";

        return $this->db->fetchAll($sql, $params, $types);
    }

    /**
     * Create izin (untuk siswa)
     */
    public function create($id_siswa, $keperluan, $rencana_keluar, $rencana_kembali) {
        // Validasi
        if (!Validator::required($keperluan)) {
            Validator::addError('keperluan', 'Alasan izin harus diisi');
            return false;
        }

        if (!Validator::dateFormat($rencana_keluar, 'Y-m-d H:i:s')) {
            Validator::addError('rencana_keluar', 'Format tanggal keluar tidak valid');
            return false;
        }

        if (!Validator::dateFormat($rencana_kembali, 'Y-m-d H:i:s')) {
            Validator::addError('rencana_kembali', 'Format tanggal kembali tidak valid');
            return false;
        }

        // Cek tanggal kembali >= tanggal keluar
        if (strtotime($rencana_kembali) < strtotime($rencana_keluar)) {
            Validator::addError('rencana_kembali', 'Tanggal kembali harus >= tanggal keluar');
            return false;
        }

        // Insert
        $sql = "INSERT INTO tb_izin (id_siswa, keperluan, rencana_keluar, rencana_kembali, status) 
                VALUES (?, ?, ?, ?, 'pending')";
        
        $this->db->query($sql, [(int)$id_siswa, $keperluan, $rencana_keluar, $rencana_kembali], 'isss');

        return $this->db->affectedRows() > 0 ? $this->db->lastInsertId() : false;
    }

    /**
     * Update izin (hanya jika masih pending)
     */
    public function update($id_izin, $keperluan = null, $rencana_keluar = null, $rencana_kembali = null) {
        // Cek status masih pending
        $izin = $this->findById((int)$id_izin);
        if (!$izin || $izin['status'] !== 'pending') {
            Validator::addError('status', 'Hanya izin dengan status pending yang dapat diubah');
            return false;
        }

        $updates = [];
        $params = [];
        $types = '';

        if ($keperluan !== null) {
            if (!Validator::required($keperluan)) {
                Validator::addError('keperluan', 'Alasan izin harus diisi');
                return false;
            }
            $updates[] = "keperluan = ?";
            $params[] = $keperluan;
            $types .= 's';
        }

        if ($rencana_keluar !== null) {
            if (!Validator::dateFormat($rencana_keluar, 'Y-m-d H:i:s')) {
                Validator::addError('rencana_keluar', 'Format tanggal keluar tidak valid');
                return false;
            }
            $updates[] = "rencana_keluar = ?";
            $params[] = $rencana_keluar;
            $types .= 's';
        }

        if ($rencana_kembali !== null) {
            if (!Validator::dateFormat($rencana_kembali, 'Y-m-d H:i:s')) {
                Validator::addError('rencana_kembali', 'Format tanggal kembali tidak valid');
                return false;
            }
            $updates[] = "rencana_kembali = ?";
            $params[] = $rencana_kembali;
            $types .= 's';
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = (int)$id_izin;
        $types .= 'i';

        $sql = "UPDATE tb_izin SET " . implode(', ', $updates) . " WHERE id_izin = ?";
        $this->db->query($sql, $params, $types);

        return $this->db->affectedRows() > 0;
    }

    /**
     * Update status izin (untuk wali kelas/admin)
     */
    public function updateStatus($id_izin, $status, $id_approve) {
        $validStatuses = ['pending', 'diizinkan', 'ditolak'];
        if (!in_array($status, $validStatuses)) {
            Validator::addError('status', 'Status tidak valid');
            return false;
        }

        $sql = "UPDATE tb_izin SET status = ?, id_approve = ? WHERE id_izin = ?";
        $this->db->query($sql, [$status, (int)$id_approve, (int)$id_izin], 'sii');

        return $this->db->affectedRows() > 0;
    }

    /**
     * Delete izin
     */
    public function delete($id_izin) {
        // Cek hanya bisa delete jika status pending
        $izin = $this->findById((int)$id_izin);
        if (!$izin || $izin['status'] !== 'pending') {
            Validator::addError('status', 'Hanya izin dengan status pending yang dapat dihapus');
            return false;
        }

        $sql = "DELETE FROM tb_izin WHERE id_izin = ?";
        $this->db->query($sql, [(int)$id_izin], 'i');

        return $this->db->affectedRows() > 0;
    }

    /**
     * Delete semua izin by siswa (untuk cascading delete)
     */
    public function deleteBySiswa($id_siswa) {
        $sql = "DELETE FROM tb_izin WHERE id_siswa = ?";
        $this->db->query($sql, [(int)$id_siswa], 'i');

        return $this->db->affectedRows() > 0;
    }

    /**
     * Count izin dengan status tertentu
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM tb_izin WHERE status = ?";
        $result = $this->db->fetchOne($sql, [$status], 's');
        return $result['total'] ?? 0;
    }

    /**
     * Get statistics
     */
    public function getStats() {
        return [
            'pending' => $this->countByStatus('pending'),
            'diizinkan' => $this->countByStatus('diizinkan'),
            'ditolak' => $this->countByStatus('ditolak'),
        ];
    }
}
?>
