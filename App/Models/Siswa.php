<?php
require_once __DIR__ . '/../Config/Database.php';

class Siswa {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByUserId($id_user) {
        $stmt = $this->db->prepare("SELECT * FROM siswa WHERE id_user = ? LIMIT 1");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findById($id_siswa) {
        $stmt = $this->db->prepare("SELECT * FROM siswa WHERE id_siswa = ? LIMIT 1");
        $stmt->bind_param("i", $id_siswa);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateFromAdmin($id_user, $nama, $kelas) {
        $stmt = $this->db->prepare("UPDATE siswa SET nama_siswa = ?, kelas = ? WHERE id_user = ?");
        $stmt->bind_param("ssi", $nama, $kelas, $id_user);
        return $stmt->execute();
    }

    public function create($id_user, $nama_siswa, $nisn, $kelas, $alamat) {
        $stmt = $this->db->prepare("INSERT INTO siswa (id_user, nama_siswa, nisn, kelas, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_user, $nama_siswa, $nisn, $kelas, $alamat);
        return $stmt->execute();
    }

    public function deleteByUserId($id_user) {
        $stmt = $this->db->prepare("DELETE FROM siswa WHERE id_user = ?");
        $stmt->bind_param("i", $id_user);
        return $stmt->execute();
    }

}
?>
