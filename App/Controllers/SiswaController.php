<?php
require_once __DIR__ . '/../Models/Izin.php';
require_once __DIR__ . '/../Models/Siswa.php';

class SiswaController {

  // ───────────────────────────────────────────────
  // Helper: pastikan user login
  private function auth() {
    if (!isset($_SESSION['user'])) {
      header('Location: ../Public/index.php');
      exit;
    }
  }

  // Helper: ambil data siswa berdasarkan user login
  private function getLoggedSiswa() {
    $u = $_SESSION['user'];
    $sM = new Siswa();
    return $sM->findByUserId($u['id_user']);
  }

  // Helper: Set flash message
  private function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
  }

  // ───────────────────────────────────────────────
  public function dashboard() {
    $this->auth();

    $s = $this->getLoggedSiswa();
    $izinM = new Izin();
    $list = $izinM->getBySiswa($s['id_siswa'] ?? 0);

    include __DIR__ . '/../Views/siswa/dashboard.php';
  }

  // ───────────────────────────────────────────────
  public function index() {
    $this->auth();

    $s = $this->getLoggedSiswa();
    $izinM = new Izin();
    $data = $izinM->getBySiswa($s['id_siswa'] ?? 0);

    include __DIR__ . '/../Views/siswa/index.php';
  }

  // ───────────────────────────────────────────────
  public function izin() {
    $this->auth();

    $s = $this->getLoggedSiswa();
    $izinM = new Izin();
    $data = $izinM->getBySiswa($s['id_siswa'] ?? 0);

    include __DIR__ . '/../Views/siswa/index.php';
  }

  // ───────────────────────────────────────────────
  public function create() {
    $this->auth();
    include __DIR__ . '/../Views/siswa/create.php';
  }

  // ───────────────────────────────────────────────
  public function store() {
    $this->auth();

    $s = $this->getLoggedSiswa();
    if (!$s || !isset($s['id_siswa'])) {
      $this->setFlash('error', 'Data siswa tidak ditemukan. Silakan hubungi admin.');
      header('Location: ../Public/index.php?c=siswa&m=create');
      exit;
    }
    $id_siswa = $s['id_siswa'];

    // Ambil input
    $kep     = trim($_POST['keperluan'] ?? '');
    $keluar  = $_POST['keluar'] ?? '';
    $kembali = $_POST['kembali'] ?? '';

    // Validasi
    if (strlen($kep) < 5) {
      $this->setFlash('error', 'Keperluan minimal 5 karakter');
      header('Location: ../Public/index.php?c=siswa&m=create');
      exit;
    }

    if (strtotime($kembali) < strtotime($keluar)) {
      $this->setFlash('error', 'Rencana kembali harus setelah rencana keluar');
      header('Location: ../Public/index.php?c=siswa&m=create');
      exit;
    }

    // Simpan izin
    $izinM = new Izin();
    if ($izinM->create($id_siswa, $kep, $keluar, $kembali)) {
      $this->setFlash('success', 'Izin berhasil diajukan');
    } else {
      $this->setFlash('error', 'Gagal mengajukan izin');
    }

    header('Location: ../Public/index.php?c=siswa&m=izin');
    exit;
  }

  // ───────────────────────────────────────────────
  public function delete() {
    $this->auth();

    $id = (int)($_GET['id'] ?? 0);

    $izinM = new Izin();
    if ($izinM->delete($id)) {
      $this->setFlash('success', 'Izin dihapus');
    } else {
      $this->setFlash('error', 'Gagal menghapus izin');
    }

    header('Location: ../Public/index.php?c=siswa&m=izin');
    exit;
  }

  // ───────────────────────────────────────────────
  public function edit() {
    $this->auth(); // Pastikan siswa sudah login

    $id = (int)($_GET['id'] ?? 0);

    $s = $this->getLoggedSiswa();
    $izinM = new Izin();

    // Ambil data izin berdasarkan id_izin
    $izin = $izinM->getBySiswa($s['id_siswa']);

    // Cari izin berdasarkan id_izin dan status pending
    $izinToEdit = null;
    foreach ($izin as $z) {
        if ($z['id_izin'] == $id && $z['status'] == 'pending') {
            $izinToEdit = $z;
            break;
        }
    }

    // Jika izin ditemukan dan statusnya pending, tampilkan form edit
    if ($izinToEdit) {
        include __DIR__ . '/../Views/siswa/edit.php';
    } else {
        $this->setFlash('error', 'Izin tidak ditemukan atau sudah diproses');
        header('Location: ../Public/index.php?c=siswa&m=izin');
        exit;
    }
  }

  // ───────────────────────────────────────────────
  public function update() {
    $this->auth(); // Pastikan siswa sudah login

    $s = $this->getLoggedSiswa();
    $id_siswa = $s['id_siswa'] ?? 0;

    // Ambil input dari form
    $id_izin = (int)($_POST['id_izin'] ?? 0);
    $keperluan = trim($_POST['keperluan'] ?? '');
    $keluar = $_POST['keluar'] ?? '';
    $kembali = $_POST['kembali'] ?? '';

    // Validasi input
    if (strlen($keperluan) < 5) {
        $this->setFlash('error', 'Keperluan minimal 5 karakter');
        header("Location: ../Public/index.php?c=siswa&m=edit&id=$id_izin");
        exit;
    }

    if (strtotime($kembali) < strtotime($keluar)) {
        $this->setFlash('error', 'Rencana kembali harus setelah rencana keluar');
        header("Location: ../Public/index.php?c=siswa&m=edit&id=$id_izin");
        exit;
    }

    // Update izin
    $izinM = new Izin();
    if ($izinM->updateIzin($id_izin, $keperluan, $keluar, $kembali)) {
        $this->setFlash('success', 'Izin berhasil diperbarui');
    } else {
        $this->setFlash('error', 'Gagal memperbarui izin');
    }

    header('Location: ../Public/index.php?c=siswa&m=izin');
    exit;
  }
}
?>
