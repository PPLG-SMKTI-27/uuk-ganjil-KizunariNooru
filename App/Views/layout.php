<?php
if(!isset($_SESSION)) session_start();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sistem Perizinan</title>
  <link href="<?= BASE_URL ?>css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="bg-white shadow">
  <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
    <div class="flex items-center gap-4">
      <a href="<?= BASE_URL ?>" class="font-bold text-lg">Perizinan</a>
      <?php if(isset($_SESSION['user'])): ?>
        <?php if($_SESSION['user']['role'] === 'Siswa'): ?>
          <a href="<?= BASE_URL ?>?c=siswa&m=dashboard" class="text-sm text-gray-600">Dashboard</a>
          <a href="<?= BASE_URL ?>?c=siswa&m=index" class="text-sm text-gray-600">Izin Saya</a>
        <?php elseif($_SESSION['user']['role'] === 'WaliKelas'): ?>
          <a href="<?= BASE_URL ?>?c=wali&m=index" class="text-sm text-gray-600">Daftar Izin</a>
        <?php elseif($_SESSION['user']['role'] === 'Admin'): ?>
          <a href="<?= BASE_URL ?>?c=admin&m=index" class="text-sm text-gray-600">Manajemen User</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <div>
      <?php if(isset($_SESSION['user'])): ?>
        <span class="text-sm text-gray-700 mr-3"><?= htmlspecialchars($_SESSION['user']['email']) ?></span>
        <a href="<?= BASE_URL ?>?c=auth&m=logout" class="px-3 py-1 bg-red-500 text-white rounded">Logout</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>?c=auth&m=loginView" class="px-3 py-1 bg-blue-600 text-white rounded">Login</a>
      <?php endif;?>
    </div>
  </div>
</nav>

<div class="max-w-6xl mx-auto p-4">
  <?php if($flash): ?>
    <div class="mb-4">
      <div class="<?= $flash['type']=='success' ? 'bg-green-100 text-green-800':'bg-red-100 text-red-800' ?> p-3 rounded">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    </div>
  <?php endif; ?>
  <div>
    <?= $content ?? '' ?>
  </div>
</div>
</body>
</html>
