<?php ob_start(); ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

  <!-- Total izin -->
  <div class="bg-white p-6 rounded-lg shadow-lg">
    <h3 class="text-sm text-gray-500">Izin Diajukan</h3>
    <p class="text-3xl font-bold text-gray-800">
      <?= isset($list) ? count($list) : 0 ?>
    </p>
  </div>

  <!-- Izin terakhir -->
  <div class="bg-white p-6 rounded-lg shadow-lg">
    <h3 class="text-sm text-gray-500">Izin Terakhir</h3>

    <?php if (!empty($list)): ?>
        <p class="text-lg font-semibold text-gray-800">
            <?= htmlspecialchars($list[0]['keperluan']) ?>
        </p>
        <p class="text-sm text-gray-400">
            Status: <?= htmlspecialchars($list[0]['status']) ?>
        </p>
    <?php else: ?>
        <p class="text-gray-400">Belum ada izin</p>
    <?php endif; ?>
  </div>

  <!-- Tombol Buat Izin -->
  <div class="bg-white p-6 rounded-lg shadow-lg flex items-center justify-center">
    <a href="<?= BASE_URL ?>?c=siswa&m=create" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300">
      Ajukan Izin
    </a>
  </div>
</div>

<div class="mt-6 text-center">
  <a href="<?= BASE_URL ?>?c=siswa&m=izin" class="text-blue-600 hover:underline">
    Kelola Izin Saya →
  </a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
