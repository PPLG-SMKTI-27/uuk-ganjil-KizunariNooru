<?php
/**
 * Izin Controller (Deprecated)
 * Controller ini sudah tidak digunakan. Gunakan SiswaController untuk aksi siswa.
 * Controller ini dipertahankan untuk kompatibilitas lama.
 */

require_once __DIR__ . '/../Models/Izin.php';
require_once __DIR__ . '/../Models/Siswa.php';
require_once __DIR__ . '/../Middleware/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class IzinController {
    /**
     * Redirect ke SiswaController
     */
    public function index() {
        header('Location: ' . BASE_URL . 'index.php?action=siswa.history');
        exit;
    }

    /**
     * Redirect ke SiswaController
     */
    public function create() {
        header('Location: ' . BASE_URL . 'index.php?action=siswa.create');
        exit;
    }

    /**
     * Redirect ke SiswaController
     */
    public function store() {
        header('Location: ' . BASE_URL . 'index.php?action=siswa.create');
        exit;
    }

    /**
     * Redirect ke SiswaController
     */
    public function delete() {
        header('Location: ' . BASE_URL . 'index.php?action=siswa.deleteIzin&id=' . ($_GET['id'] ?? ''));
        exit;
    }
}
