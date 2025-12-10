<?php ob_start(); ?>

<div class="max-w-lg mx-auto bg-white p-6 rounded shadow space-y-4">
  <h2 class="text-2xl font-semibold mb-4">Edit User</h2>

  <?php if (isset($_SESSION['error'])): ?>
    <p class="p-2 bg-red-100 text-red-600 rounded"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <p class="p-2 bg-green-100 text-green-600 rounded"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>index.php?action=admin.update" method="post" class="space-y-3">
    <?= Csrf::field() ?>
    
    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id_user'] ?? '') ?>">

    <div>
      <label class="font-medium">Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required class="w-full border p-2 rounded">
    </div>

    <div>
      <label class="font-medium">Role</label>
      <select name="role" id="role-select" class="w-full border p-2 rounded">
        <option value="Admin" <?= ($user['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="WaliKelas" <?= ($user['role'] ?? '') === 'WaliKelas' ? 'selected' : '' ?>>Wali Kelas</option>
        <option value="Siswa" <?= ($user['role'] ?? '') === 'Siswa' ? 'selected' : '' ?>>Siswa</option>
      </select>
    </div>

    <div>
      <label class="font-medium">Password Baru (opsional)</label>
      <input name="password" type="password" placeholder="Kosongkan jika tidak diganti" class="w-full border p-2 rounded" minlength="6">
      <small class="text-gray-500">Minimal 6 karakter</small>
    </div>

    <?php if(($user['role'] ?? '') === 'Siswa' && isset($siswa)): ?>
      <div>
        <label class="font-medium">Nama Siswa</label>
        <input name="nama_siswa" class="w-full border p-2 rounded" value="<?= htmlspecialchars($siswa['nama_siswa'] ?? '') ?>">
      </div>

      <div>
        <label class="font-medium">NISN</label>
        <input name="nisn" class="w-full border p-2 rounded" value="<?= htmlspecialchars($siswa['nisn'] ?? '') ?>" maxlength="10">
      </div>

      <div>
        <label class="font-medium">Kelas</label>
        <input name="kelas" class="w-full border p-2 rounded" value="<?= htmlspecialchars($siswa['kelas'] ?? '') ?>">
      </div>

      <div>
        <label class="font-medium">Alamat</label>
        <textarea name="alamat" class="w-full border p-2 rounded" rows="3"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
      </div>
    <?php endif; ?>

    <div class="flex gap-2">
      <button type="submit" class="flex-1 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
        Update
      </button>
      <a href="<?= BASE_URL ?>index.php?action=admin.index" class="flex-1 bg-gray-600 text-white p-2 rounded hover:bg-gray-700 text-center">
        Batal
      </a>
    </div>
  </form>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
