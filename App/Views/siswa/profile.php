<?php
/**
 * Siswa Profile View
 * Halaman profil siswa
 */

ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Profil Saya</h2>
    <?php if (isset($siswa)): ?>
    <a href="<?= BASE_URL ?>index.php?action=siswa.profile" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
      Edit Profil
    </a>
    <?php endif; ?>
  </div>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <!-- Edit Mode -->
    <form method="POST" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= Csrf::token() ?>">
      
      <div class="form-group">
        <label class="form-label">Nama Siswa</label>
        <input type="text" name="nama_siswa" value="<?= htmlspecialchars($siswa['nama_siswa'] ?? '') ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
      </div>

      <div class="form-group">
        <label class="form-label">NISN</label>
        <input type="text" name="nisn" value="<?= htmlspecialchars($siswa['nisn'] ?? '') ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
      </div>

      <div class="form-group">
        <label class="form-label">NIK</label>
        <input type="text" name="nik" value="<?= htmlspecialchars($siswa['nik'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
      </div>

      <div class="form-group">
        <label class="form-label">Kelas</label>
        <input type="text" name="kelas" value="<?= htmlspecialchars($siswa['kelas'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
      </div>

      <div class="form-group">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
      </div>

      <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
          Simpan
        </button>
        <a href="<?= BASE_URL ?>index.php?action=siswa.dashboard" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition">
          Batal
        </a>
      </div>
    </form>

  <?php else: ?>
    <!-- View Mode -->
    <div class="space-y-4">
      <div class="border-b pb-4">
        <p class="text-sm text-gray-500">Nama Siswa</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($siswa['nama_siswa'] ?? '-') ?></p>
      </div>

      <div class="border-b pb-4">
        <p class="text-sm text-gray-500">NISN</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($siswa['nisn'] ?? '-') ?></p>
      </div>

      <div class="border-b pb-4">
        <p class="text-sm text-gray-500">NIK</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($siswa['nik'] ?? '-') ?></p>
      </div>

      <div class="border-b pb-4">
        <p class="text-sm text-gray-500">Kelas</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($siswa['kelas'] ?? '-') ?></p>
      </div>

      <div class="border-b pb-4">
        <p class="text-sm text-gray-500">Alamat</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($siswa['alamat'] ?? '-') ?></p>
      </div>

      <div class="border-b pb-4">
        <p class="text-sm text-gray-500">Email</p>
        <p class="text-lg font-semibold"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
      </div>

      <div class="mt-6 flex gap-2">
        <button onclick="document.getElementById('editForm').style.display='block'" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
          Edit Profil
        </button>
        <a href="<?= BASE_URL ?>index.php?action=siswa.dashboard" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition">
          Kembali
        </a>
      </div>
    </div>

  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
