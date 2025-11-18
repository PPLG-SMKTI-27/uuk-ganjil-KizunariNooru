<?php
require_once __DIR__ . '/../Config/Database.php';

class Izin {
    private $db;

    public function __construct(){
        $this->db = Database::connect(); 
    }

    // Fungsi untuk mengecek apakah pegawai dengan id_user tertentu ada dan jabatan WaliKelas
    private function isPegawaiExist($id_user) {
        $stmt = $this->db->prepare("SELECT id_pegawai FROM pegawai WHERE id_user = ? AND jabatan = 'WaliKelas' LIMIT 1");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id_pegawai'];
        }
        return false;
    }

    // Fungsi untuk update status izin
    public function updateStatus($id, $status, $waliId, $komentar = null){
        $id = (int)$id;
        $waliId = (int)$waliId;

        $validStatuses = ['pending', 'diizinkan', 'ditolak'];
        if (!in_array($status, $validStatuses)) {
            echo "Status yang diberikan tidak valid.";
            return false;
        }

        $idPegawai = $this->isPegawaiExist($waliId);
        if (!$idPegawai) {
            echo "ID Pegawai (Wali) tidak ditemukan.";
            return false;
        }

        $status = $this->db->real_escape_string($status);
        $komentar = $komentar ? $this->db->real_escape_string($komentar) : null;

        $query = "UPDATE tb_izin SET status=?, id_approve=?, komentar_wali=? WHERE id_izin=?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo "Error preparing the query: " . $this->db->error;
            return false;
        }

        $stmt->bind_param("sisi", $status, $idPegawai, $komentar, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Terjadi kesalahan saat mengupdate status izin: " . $stmt->error;
            return false;
        }
    }

    public function getBySiswa($id_siswa){
        $id = (int)$id_siswa;
        $q = $this->db->query("SELECT tb_izin.*, siswa.kelas, siswa.nisn, siswa.nik, siswa.alamat 
                               FROM tb_izin 
                               JOIN siswa ON tb_izin.id_siswa = siswa.id_siswa 
                               WHERE tb_izin.id_siswa = $id 
                               ORDER BY tb_izin.id_izin DESC");
        return $q ? $q->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function create($id_siswa, $keperluan, $keluar, $kembali){
        $id = (int)$id_siswa;
        $keperluan = $this->db->real_escape_string($keperluan);
        $keluar = $this->db->real_escape_string($keluar);
        $kembali = $this->db->real_escape_string($kembali);

        return $this->db->query("INSERT INTO tb_izin (id_siswa, keperluan, rencana_keluar, rencana_kembali, status) 
                                 VALUES ($id, '$keperluan', '$keluar', '$kembali', 'pending')");
    }

    public function delete($id){
        $id = (int)$id;
        return $this->db->query("DELETE FROM tb_izin WHERE id_izin=$id");
    }

    public function deleteBySiswa($id_siswa){
        $id = (int)$id_siswa;
        return $this->db->query("DELETE FROM tb_izin WHERE id_siswa=$id");
    }

    public function getAllWithSiswa($filterStatus = null, $qname = null){
        $where = [];
        if ($filterStatus) $where[] = "tb_izin.status='" . $this->db->real_escape_string($filterStatus) . "'";
        if ($qname) $where[] = "siswa.nama_siswa LIKE '%" . $this->db->real_escape_string($qname) . "%'";
        
        $sql = "SELECT tb_izin.*, siswa.nama_siswa, siswa.kelas 
                FROM tb_izin 
                JOIN siswa ON tb_izin.id_siswa = siswa.id_siswa";
        if (count($where)) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY tb_izin.id_izin DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id_izin){
        $id_izin = (int)$id_izin;
        $q = $this->db->query("SELECT * FROM tb_izin WHERE id_izin = $id_izin");
        return $q ? $q->fetch_assoc() : null;
    }

    // Fungsi untuk update izin 
    public function updateIzin($id_izin, $keperluan, $keluar, $kembali) {
        $id_izin = (int)$id_izin;
        $keperluan = $this->db->real_escape_string($keperluan);
        $keluar = $this->db->real_escape_string($keluar);
        $kembali = $this->db->real_escape_string($kembali);
    
        // Query untuk update data izin dengan id_izin tertentu
        $query = "UPDATE tb_izin SET keperluan='$keperluan', rencana_keluar='$keluar', rencana_kembali='$kembali' WHERE id_izin=$id_izin AND status='pending'";
    
        return $this->db->query($query);
    }

}
?>
