<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Siswa.php';

class AdminController {
    public function index(){
      if(!isset($_SESSION['user'])){ header('Location: ../Public/index.php'); exit; }
      $u = new User(); $users = $u->getAll();
      include __DIR__ . '/../Views/admin/index.php';
    }
    public function createUser(){
      if(!isset($_SESSION['user'])){ header('Location: ../Public/index.php'); exit; }
      // form view
      include __DIR__ . '/../Views/admin/create_user.php';
    }
    public function storeUser(){
        // Ambil data dari form
        $nama = $_POST['nama'] ?? '';
        $email = $_POST['email'];
        $role = $_POST['role'];
        $pw = $_POST['password'] ?: bin2hex(random_bytes(4)); // default password jika tidak diisi

        // Cek apakah email sudah ada
        $u = new User();
        $existingUser = $u->findByEmail($email);
        if ($existingUser) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Email sudah digunakan'];
            header('Location: ../Public/index.php?c=admin&m=createUser');
            exit;
        }

        // Membuat user
        $id_user = $u->create($email, $pw, $role); // membuat user baru

        // Jika user berhasil dibuat dan role adalah Siswa
        if ($id_user && $role === 'Siswa') {
            // Ambil data siswa dari form
            $nama_siswa = $_POST['nama_siswa'];
            $nisn = $_POST['nisn'];
            $nik = $_POST['nik'];
            $kelas = $_POST['kelas'];
            $alamat = $_POST['alamat'];

            // Buat data siswa baru
            $s = new Siswa();
            $s->create($id_user, $nama_siswa, $nisn, $kelas, $alamat);

            $_SESSION['flash'] = ['type' => 'success', 'msg' => "User dan data siswa berhasil dibuat (password: $pw)"];
        } elseif ($id_user) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "User berhasil dibuat (password: $pw)"];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Gagal membuat user'];
        }

        // Redirect ke halaman utama
        header('Location: ../Public/index.php?c=admin&m=index');
        exit;
    }

    public function deleteUser() {
        $id = (int)($_GET['id'] ?? 0);

        // Hapus data izin yang terkait dengan siswa
        require_once __DIR__ . '/../Models/Izin.php';
        $izin = new Izin();
        $siswa = new Siswa();
        $siswaData = $siswa->findByUserId($id);
        if ($siswaData) {
            $izin->deleteBySiswa($siswaData['id_siswa']);
        }

        // Hapus data siswa yang terkait dengan user
        $siswa->deleteByUserId($id);

        // Hapus data user
        $user = new User();
        $user->delete($id);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'User dan data terkait dihapus'];
        header('Location: ../Public/index.php?c=admin&m=index');
    }
  
    public function editUser(){
        $id = (int)($_GET['id'] ?? 0);
        $u = new User();
        $user = $u->find($id);
    
        // jika role siswa → ambil detail siswa
        $siswa = null;
        if($user['role'] === 'Siswa'){
            $s = new Siswa();
            $siswa = $s->findByUserId($id);
        }
    
        include __DIR__ . '/../Views/admin/edit_user.php';
    }
    
    public function updateUser(){
        $id_user = (int)$_POST['id_user'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = $_POST['password'];
    
        $u = new User();
        $u->updateUser($id_user, $email, $role, $password ?: null);
    
        // update siswa jika role siswa
        if($role === 'Siswa'){
            $nama = $_POST['nama_siswa'];
            $kelas = $_POST['kelas'];
            $s = new Siswa();
            $s->updateFromAdmin($id_user, $nama, $kelas);
        }
    
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'User berhasil diperbarui'];
        header('Location: ../Public/index.php?c=admin&m=index');
        exit;
    }

}
