<?php
require_once __DIR__ . '/../Models/Izin.php';

class WaliController {
    public function index(){
        if (!isset($_SESSION['user'])) {
            header('Location: ../Public/index.php');
            exit;
        }

        $filter = $_GET['status'] ?? null;
        $q = $_GET['q'] ?? null;
        
        $izinM = new Izin();
        $data = $izinM->getAllWithSiswa($filter, $q);

        include __DIR__ . '/../Views/wali/index.php';
    }

    public function proses() {
        if (!isset($_SESSION['user'])) {
            header('Location: ../Public/index.php');
            exit;
        }

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);  
        $status = $_POST['status'] ?? $_GET['status'] ?? 'pending';
        $komentar = $_POST['komentar'] ?? null;

        $validStatuses = ['pending', 'diizinkan', 'ditolak'];
        if (!in_array($status, $validStatuses)) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Status tidak valid'];
            header('Location: ../Public/index.php?c=wali&m=index');
            exit;
        }

        $waliId = $_SESSION['user']['id_user'];

        $izinM = new Izin();
        $result = $izinM->updateStatus($id, $status, $waliId, $komentar);

        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Status izin berhasil diperbarui'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Terjadi kesalahan saat memperbarui status izin'];
        }

        header('Location: ../Public/index.php?c=wali&m=index');
        exit;
    }

    public function detail() {
        $id_izin = $_GET['id_izin'] ?? null;

        if ($id_izin) {
            $izinModel = new Izin();
            $izinDetail = $izinModel->getById($id_izin);

            if ($izinDetail) {
                // Ambil data siswa berdasarkan id_siswa dari izin
                require_once __DIR__ . '/../Models/Siswa.php';
                $siswaModel = new Siswa();
                $siswaDetail = $siswaModel->findById($izinDetail['id_siswa']);

                // Gabungkan data izin dan siswa
                $izinDetail = array_merge($izinDetail, $siswaDetail ?? []);

                include __DIR__ . '/../Views/wali/detail.php';
            } else {
                echo "Data izin tidak ditemukan.";
            }
        } else {
            echo "ID izin tidak valid.";
        }
    }

    public function delete() {
        if (!isset($_SESSION['user'])) {
            header('Location: ../Public/index.php');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);

        $izinM = new Izin();
        if ($izinM->delete($id)) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Izin berhasil dihapus'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Gagal menghapus izin'];
        }

        header('Location: ../Public/index.php?c=wali&m=index');
        exit;
    }
}
?>
