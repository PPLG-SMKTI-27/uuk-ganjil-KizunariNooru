<?php
/**
 * Siswa Controller
 * Mengelola aksi siswa: dashboard, ajukan izin, riwayat, profil
 */

require_once __DIR__ . '/../Models/Izin.php';
require_once __DIR__ . '/../Models/Siswa.php';
require_once __DIR__ . '/../Middleware/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class SiswaController {
    /**
     * Dashboard siswa
     */
    public      function dashboard() {
        Guard::requireRole('Siswa');
        include __DIR__ . '/../Views/siswa/dashboard.php';
    }

    /**
     * Halaman form ajukan izin
     */
    public function create() {
        Guard::requireRole('Siswa');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->storeIzin();
        }

        include __DIR__ . '/../Views/siswa/create.php';
    }

    /**
     * Process ajukan izin
     */
    private function storeIzin() {
        // Verify CSRF
        if (!isset($_POST['csrf_token']) || !Csrf::verify($_POST['csrf_token'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Invalid CSRF token'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.create');
            exit;
        }

        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);

        // Validation
        $errors = [];
        $keperluan = Sanitizer::string($_POST['keperluan'] ?? '');
        $rencana_keluar_raw = trim($_POST['rencana_keluar'] ?? '');
        $rencana_kembali_raw = trim($_POST['rencana_kembali'] ?? '');

        // Convert HTML5 datetime-local input (YYYY-MM-DDTHH:MM) to MySQL datetime
        $rencana_keluar = $rencana_keluar_raw ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $rencana_keluar_raw))) : '';
        $rencana_kembali = $rencana_kembali_raw ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $rencana_kembali_raw))) : '';

        if (!Validator::required($keperluan)) {
            $errors['keperluan'] = 'Alasan izin harus diisi';
        }

        if (!Validator::dateFormat($rencana_keluar, 'Y-m-d H:i:s')) {
            $errors['rencana_keluar'] = 'Format tanggal keluar tidak valid';
        }

        if (!Validator::dateFormat($rencana_kembali, 'Y-m-d H:i:s')) {
            $errors['rencana_kembali'] = 'Format tanggal kembali tidak valid';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            header('Location: ' . BASE_URL . 'index.php?action=siswa.create');
            exit;
        }

        // Create izin
        $izinModel = new Izin();
        $result = $izinModel->create($siswa['id_siswa'], $keperluan, $rencana_keluar, $rencana_kembali);

        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Izin berhasil diajukan. Tunggu persetujuan wali kelas.'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.dashboard');
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Gagal mengajukan izin'];
            $_SESSION['form_errors'] = $errors; // Use local errors instead of Validator::errors()
            header('Location: ' . BASE_URL . 'index.php?action=siswa.create');
        }
        exit;
    }

    /**
     * Halaman riwayat izin
     */
    public function history() {
        Guard::requireRole('Siswa');

        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);

        $izinModel = new Izin();
        $data = $izinModel->getBySiswa($siswa['id_siswa']);

        include __DIR__ . '/../Views/siswa/index.php';
    }

    /**
     * Show edit form for a izin (only owner and pending)
     */
    public function edit() {
        Guard::requireRole('Siswa');

        $izinId = (int)($_GET['id'] ?? 0);
        if (!$izinId) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin tidak ditemukan'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        $izinModel = new Izin();
        $izin = $izinModel->findById($izinId);
        if (!$izin) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin tidak ditemukan'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        // Ownership check
        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);
        if (!$siswa || $izin['id_siswa'] != $siswa['id_siswa']) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Akses ditolak'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        // Only pending can be edited
        if ($izin['status'] !== 'pending') {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Hanya izin pending yang dapat diedit'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        $izinToEdit = $izin;
        include __DIR__ . '/../Views/siswa/edit.php';
    }

    /**
     * Update izin after editing
     */
    public function update() {
        Guard::requireRole('Siswa');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Invalid CSRF token'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        $id_izin = (int)($_POST['id_izin'] ?? 0);
        $keperluan = Sanitizer::string($_POST['keperluan'] ?? '');
        $rencana_keluar = trim($_POST['rencana_keluar'] ?? '');
        $rencana_kembali = trim($_POST['rencana_kembali'] ?? '');

        $izinModel = new Izin();
        $izin = $izinModel->findById($id_izin);
        if (!$izin) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin tidak ditemukan'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        // Ownership
        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);
        if ($izin['id_siswa'] != $siswa['id_siswa']) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Akses ditolak'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        $result = $izinModel->update($id_izin, $keperluan, $rencana_keluar, $rencana_kembali);
        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Izin berhasil diperbarui'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Gagal memperbarui izin: ' . implode('; ', (array)Validator::errors())];
        }

        header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
        exit;
    }

    /**
     * Halaman profil siswa
     */
    public function profile() {
        Guard::requireRole('Siswa');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->updateProfile();
        }

        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);

        include __DIR__ . '/../Views/siswa/profile.php';
    }

    /**
     * Update profil siswa
     */
    private function updateProfile() {
        // Verify CSRF
        if (!isset($_POST['csrf_token']) || !Csrf::verify($_POST['csrf_token'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Invalid CSRF token'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.profile');
            exit;
        }

        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);

        // Validation
        $errors = [];
        $nama_siswa = Sanitizer::string($_POST['nama_siswa'] ?? '');
        $alamat = Sanitizer::string($_POST['alamat'] ?? '');

        if (!Validator::required($nama_siswa)) {
            $errors['nama_siswa'] = 'Nama harus diisi';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            header('Location: ' . BASE_URL . 'index.php?action=siswa.profile');
            exit;
        }

        // Update
        $result = $siswaModel->update($siswa['id_siswa'], $nama_siswa, null, null, $alamat);

        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Profil berhasil diperbarui'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.profile');
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Gagal memperbarui profil'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.profile');
        }
        exit;
    }

    /**
     * Delete izin (hanya izin pending)
     */
    public function delete() {
        Guard::requireRole('Siswa');

        if (!isset($_GET['id'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'ID izin tidak valid'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        $id_izin = (int)$_GET['id'];

        // Cek owner
        $izinModel = new Izin();
        $izin = $izinModel->findById($id_izin);

        if (!$izin) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin tidak ditemukan'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        // Verify ownership
        $user = Auth::user();
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findByUserId($user['id_user']);

        if ($izin['id_siswa'] != $siswa['id_siswa']) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Akses ditolak'];
            header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
            exit;
        }

        // Delete
        $result = $izinModel->delete($id_izin);

        if ($result) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Izin berhasil dihapus'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Hanya izin dengan status pending yang dapat dihapus'];
        }

        header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
        exit;
    }
}
