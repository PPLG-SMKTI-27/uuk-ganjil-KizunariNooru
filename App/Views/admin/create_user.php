<?php ob_start(); ?>
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow space-y-4">
  <h2 class="text-2xl font-semibold">Tambah User Baru</h2>

  <?php if (isset($_SESSION['error'])): ?>
    <p class="p-2 bg-red-100 text-red-600 rounded"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
    <?php foreach($_SESSION['errors'] as $field => $messages): ?>
      <?php foreach((array)$messages as $msg): ?>
        <p class="p-2 bg-red-100 text-red-600 rounded"><?= htmlspecialchars($msg) ?></p>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>index.php?action=admin.store" method="post" class="space-y-3">
    <?= Csrf::field() ?>
    
    <div>
      <label class="font-medium">Email</label>
      <input type="email" name="email" class="w-full border p-2 rounded" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
    </div>

    <div>
      <label class="font-medium">Password</label>
      <input type="password" name="password" class="w-full border p-2 rounded" required minlength="6">
      <small class="text-gray-500">Minimal 6 karakter</small>
    </div>

    <div>
      <label class="font-medium">Role</label>
      <select name="role" id="role-select" class="w-full border p-2 rounded" required>
        <option value="">- Pilih Role -</option>
        <option value="Admin" <?= ($_SESSION['old']['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="WaliKelas" <?= ($_SESSION['old']['role'] ?? '') === 'WaliKelas' ? 'selected' : '' ?>>Wali Kelas</option>
        <option value="Siswa" <?= ($_SESSION['old']['role'] ?? '') === 'Siswa' ? 'selected' : '' ?>>Siswa</option>
      </select>
    </div>

    <!-- Fields for Siswa (will only show if role is "Siswa") -->
    <div id="siswa-fields" class="hidden space-y-3">
      <div>
        <label class="font-medium">Nama Siswa</label>
        <input type="text" name="nama_siswa" class="w-full border p-2 rounded" value="<?= htmlspecialchars($_SESSION['old']['nama_siswa'] ?? '') ?>">
      </div>

      <div>
        <label class="font-medium">NISN</label>
        <input type="text" name="nisn" class="w-full border p-2 rounded" maxlength="10" value="<?= htmlspecialchars($_SESSION['old']['nisn'] ?? '') ?>" placeholder="10 digit">
      </div>

      <div>
        <label class="font-medium">NIK</label>
        <input type="text" name="nik" class="w-full border p-2 rounded" maxlength="16" value="<?= htmlspecialchars($_SESSION['old']['nik'] ?? '') ?>">
      </div>

      <div>
        <label class="font-medium">Kelas</label>
        <input type="text" name="kelas" class="w-full border p-2 rounded" value="<?= htmlspecialchars($_SESSION['old']['kelas'] ?? '') ?>">
      </div>

      <div>
        <label class="font-medium">Alamat</label>
        <textarea name="alamat" class="w-full border p-2 rounded" rows="3"><?= htmlspecialchars($_SESSION['old']['alamat'] ?? '') ?></textarea>
      </div>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="flex-1 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
        Simpan User
      </button>
      <a href="<?= BASE_URL ?>index.php?action=admin.index" class="flex-1 bg-gray-600 text-white p-2 rounded hover:bg-gray-700 text-center">
        Batal
      </a>
    </div>
  </form>
</div>

<script>
  const roleSelect = document.getElementById('role-select');
  const siswaFields = document.getElementById('siswa-fields');
  
  function toggleSiswaFields() {
    if (roleSelect.value === 'Siswa') {
      siswaFields.classList.remove('hidden');
    } else {
      siswaFields.classList.add('hidden');
    }
  }
  
  roleSelect.addEventListener('change', toggleSiswaFields);
  toggleSiswaFields(); // Run on page load
</script>

<?php 
$content = ob_get_clean();
unset($_SESSION['old']);
include __DIR__ . '/../layout.php';
?></script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
