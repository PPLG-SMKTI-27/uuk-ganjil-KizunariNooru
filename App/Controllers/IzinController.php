<?php
session_start();  // Pastikan session_start() ada di awal file
require_once __DIR__ . '/../Models/Izin.php';
require_once __DIR__ . '/../Config/Database.php';

class IzinController {
    public function index(){
        // Cek apakah session user ada
        if(!isset($_SESSION['user'])){
            header('Location: ../Public/index.php');
            exit;
        }
        
        $db = Database::connect();  // Pastikan koneksi database berhasil
        $user = $_SESSION['user'];

        // Ambil ID siswa berdasarkan ID user
        $s = $db->query("SELECT * FROM siswa WHERE id_user=".(int)$user['id_user'])->fetch_assoc();
        $id_siswa = $s['id_siswa'] ?? 0;
        
        // Ambil data izin siswa
        $izin = new Izin();
        $data = $izin->getBySiswa($id_siswa);
        
        // Pastikan file View bisa di-load
        include __DIR__ . '/../Views/siswa/index.php';
    }

    public function create(){
        if(!isset($_SESSION['user'])){
            header('Location: ../Public/index.php');
            exit;
        }
        include __DIR__ . '/../Views/siswa/create.php';  // Pastikan form 'create' bisa di-load
    }

    public function store(){
        if(!isset($_SESSION['user'])){
            header('Location: ../Public/index.php');
            exit;
        }
        
        $db = Database::connect();
        $user = $_SESSION['user'];

        // Ambil ID siswa
        $s = $db->query("SELECT * FROM siswa WHERE id_user=".(int)$user['id_user'])->fetch_assoc();
        $id_siswa = $s['id_siswa'] ?? 0;

        // Cek jika data POST ada
        if (isset($_POST['keperluan'], $_POST['keluar'], $_POST['kembali'])) {
            $izin = new Izin();
            $izin->create($id_siswa, $_POST['keperluan'], $_POST['keluar'], $_POST['kembali']);
        }

        header('Location: ../Public/index.php?c=izin&m=index');
        exit;
    }

    public function delete(){
        if(!isset($_SESSION['user'])){
            header('Location: ../Public/index.php');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $izin = new Izin();
            $izin->delete($id);
        }
        
        header('Location: ../Public/index.php?c=izin&m=index');
        exit;
    }
}
?>
