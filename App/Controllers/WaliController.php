<?php
/**
 * WaliController - Guru/Wali Dashboard & Approval Management
 * 
 * Mengelola persetujuan izin siswa
 * Akses ke data siswa di kelas yang diampu
 * 
 * Requirements:
 * - Guard::requireRole('WaliKelas') pada semua method
 * - CSRF verification pada POST requests
 * - Only approve/reject izin siswa di kelas yang diampu
 */

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Siswa.php';
require_once __DIR__ . '/../Models/Izin.php';
require_once __DIR__ . '/../Middleware/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class WaliController {

    /**
     * Dashboard - Show pending izin for approval
     *
     * @access WaliKelas
     */
    public function dashboard() {
        Guard::requireRole('WaliKelas');

        // Get pegawai id from user id
        $db = Database::getInstance();
        $pegawai = $db->fetchOne("SELECT id_pegawai FROM pegawai WHERE id_user = ?", [$_SESSION['user']['id_user']], 'i');
        if (!$pegawai) {
            die('Data pegawai tidak ditemukan');
        }

        $izinModel = new Izin();

        // Get pending izin for this wali's class
        $pendingIzin = $izinModel->getForWali(
            $pegawai['id_pegawai'],
            'pending'
        );

        // Get stats
        $totalPending = count($pendingIzin);
        $totalApproved = count($izinModel->getForWali(
            $pegawai['id_pegawai'],
            'diizinkan'
        ));
        $totalRejected = count($izinModel->getForWali(
            $pegawai['id_pegawai'],
            'ditolak'
        ));

        include __DIR__ . '/../Views/wali/dashboard.php';
    }

    /**
     * List all izin (with filter by status)
     * 
     * @access WaliKelas
     */
    public function index() {
        Guard::requireRole('WaliKelas');
        
        $izinModel = new Izin();
        
        // Filter by status
        $status = $_GET['status'] ?? 'all';
        $validStatuses = ['pending', 'approved', 'rejected', 'all'];
        
        if (!in_array($status, $validStatuses)) {
            $status = 'all';
        }
        
        // Get izin
        if ($status === 'all') {
            $data = $izinModel->getForWali($_SESSION['user']['id_user']);
        } else {
            $data = $izinModel->getForWali(
                $_SESSION['user']['id_user'],
                $status
            );
        }
        
        include __DIR__ . '/../Views/wali/index.php';
    }

    /**
     * Show detail of specific izin
     * 
     * @access WaliKelas
     * @param id Izin ID
     */
    public function detail() {
        Guard::requireRole('WaliKelas');
        
        $izinId = (int)($_GET['id'] ?? 0);
        if (!$izinId) {
            $_SESSION['error'] = 'Izin tidak ditemukan';
            header('Location: ' . BASE_URL . 'index.php?action=wali.index');
            exit;
        }
        
        $izinModel = new Izin();
        $izin = $izinModel->findById($izinId);
        
        if (!$izin) {
            $_SESSION['error'] = 'Izin tidak ditemukan';
            header('Location: ' . BASE_URL . 'index.php?action=wali.index');
            exit;
        }
        
        // Get siswa detail
        $siswaModel = new Siswa();
        $siswa = $siswaModel->findById($izin['id_siswa']);
        
        include __DIR__ . '/../Views/wali/detail.php';
    }

    /**
     * Approve or reject izin request (handles both actions via action POST param)
     * 
     * @access WaliKelas
     * @post id, notes, action (approve|reject)
     */
    public function approve() {
        Guard::requireRole('WaliKelas');
        
        // CSRF verification
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Invalid CSRF token'];
            header('Location: ' . BASE_URL . 'index.php?action=wali.index');
            exit;
        }
        
        $izinId = (int)($_POST['id'] ?? 0);
        $action = $_POST['action'] ?? 'approve';
        
        if (!$izinId) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin tidak valid'];
            header('Location: ' . BASE_URL . 'index.php?action=wali.index');
            exit;
        }
        
        $izinModel = new Izin();
        $izin = $izinModel->findById($izinId);
        
        if (!$izin) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin tidak ditemukan'];
            header('Location: ' . BASE_URL . 'index.php?action=wali.index');
            exit;
        }
        
        // Verify izin is pending
        if ($izin['status'] !== 'pending') {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Izin sudah diproses'];
            header('Location: ' . BASE_URL . 'index.php?action=wali.detail&id=' . $izinId);
            exit;
        }
        
        $notes = Sanitizer::string($_POST['notes'] ?? '');
        
        // Get pegawai id from user id
        $db = Database::getInstance();
        $pegawai = $db->fetchOne("SELECT id_pegawai FROM pegawai WHERE id_user = ?", [$_SESSION['user']['id_user']], 'i');
        if (!$pegawai) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Data pegawai tidak ditemukan'];
            header('Location: ' . BASE_URL . 'index.php?action=wali.index');
            exit;
        }

        // Handle approve or reject
        if ($action === 'reject') {
            // Validate notes for rejection
            if (empty($notes)) {
                $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Alasan penolakan harus diisi'];
                header('Location: ' . BASE_URL . 'index.php?action=wali.detail&id=' . $izinId);
                exit;
            }
            $izinModel->updateStatus($izinId, 'ditolak', $pegawai['id_pegawai']);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Izin berhasil ditolak'];
        } else {
            // Default to approve
            $izinModel->updateStatus($izinId, 'diizinkan', $pegawai['id_pegawai']);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Izin berhasil disetujui'];
        }
        
        header('Location: ' . BASE_URL . 'index.php?action=wali.detail&id=' . $izinId);
        exit;
    }

    /**
     * Filter izin by date range
     * 
     * @access WaliKelas
     */
    public function filter() {
        Guard::requireRole('WaliKelas');
        
        $izinModel = new Izin();
        
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        $status = $_GET['status'] ?? 'all';
        
        $izinList = $izinModel->getForWali(
            $_SESSION['user']['id_user'],
            $status === 'all' ? null : $status
        );
        
        // Filter by date range if provided
        if (!empty($startDate) && !empty($endDate)) {
            $izinList = array_filter($izinList, function($izin) use ($startDate, $endDate) {
                $izinDate = strtotime($izin['tanggal_izin']);
                $start = strtotime($startDate);
                $end = strtotime($endDate);
                return $izinDate >= $start && $izinDate <= $end;
            });
        }
        
        include __DIR__ . '/../Views/wali/filter.php';
    }

    /**
     * Export approved izin to CSV
     * 
     * @access WaliKelas
     */
    public function export() {
        Guard::requireRole('WaliKelas');
        
        $izinModel = new Izin();
        $izinList = $izinModel->getForWali(
            $_SESSION['user']['id_user'],
            'approved'
        );
        
        // Set headers for CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=approved_izin_' . date('Y-m-d_H-i-s') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['ID', 'Nama Siswa', 'Tanggal', 'Alasan', 'Status', 'Tanggal Disetujui']);
        
        // Write data
        foreach ($izinList as $izin) {
            fputcsv($output, [
                $izin['id_izin'],
                $izin['nama_siswa'] ?? $izin['id_siswa'],
                $izin['tanggal_izin'],
                $izin['alasan'],
                $izin['status'],
                $izin['approved_at'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Get statistics
     * 
     * @access WaliKelas
     */
    public function stats() {
        Guard::requireRole('WaliKelas');
        
        $izinModel = new Izin();
        
        $pending = $izinModel->getForWali(
            $_SESSION['user']['id_user'],
            'pending'
        );
        $approved = $izinModel->getForWali(
            $_SESSION['user']['id_user'],
            'approved'
        );
        $rejected = $izinModel->getForWali(
            $_SESSION['user']['id_user'],
            'rejected'
        );
        
        $stats = [
            'total_pending' => count($pending),
            'total_approved' => count($approved),
            'total_rejected' => count($rejected),
            'total_izin' => count($pending) + count($approved) + count($rejected),
        ];
        
        // Return as JSON
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
}

