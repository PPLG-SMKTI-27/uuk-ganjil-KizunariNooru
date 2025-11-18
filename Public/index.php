<?php
session_start();
require_once __DIR__ . '/../App/Config/config.php'; // hanya panggil sekali

// Ambil controller dan metode dari URL, dengan fallback ke controller 'auth' dan metode 'loginView'
$c = $_GET['c'] ?? 'auth';
$m = $_GET['m'] ?? 'loginView';

// Menentukan path file controller berdasarkan nama controller
$controllerFile = __DIR__ . '/../App/Controllers/' . ucfirst($c) . 'Controller.php';

// Cek jika file controller ada
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $class = ucfirst($c) . 'Controller';  // Nama class controller berdasarkan nama controller di URL

    // Cek jika class controller ada
    if (class_exists($class)) {
        $ctrl = new $class();  // Membuat objek controller

        // Cek jika metode yang diminta ada dalam controller
        if (method_exists($ctrl, $m)) {
            $ctrl->$m();  // Memanggil metode yang diminta
            exit;  // Keluar setelah metode dipanggil
        }
    }
}

// Jika controller atau metode tidak ditemukan, redirect ke halaman login
header('Location: ' . BASE_URL . 'index.php?c=auth&m=loginView');
exit;
