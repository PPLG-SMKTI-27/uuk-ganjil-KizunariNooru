<?php ob_start(); ?>
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow space-y-4">
  <h2 class="text-2xl font-semibold">Tambah User Baru</h2>

  <?php if (isset($error)): ?>
    <p class="p-2 bg-red-100 text-red-600 rounded"><?= $error ?></p>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>?c=admin&m=storeUser" method="post" class="space-y-3">
    <div>
      <label class="font-medium">Nama</label>
      <input name="nama" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="font-medium">Email</label>
      <input type="email" name="email" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="font-medium">Password</label>
      <input type="password" name="password" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="font-medium">Role</label>
      <select name="role" class="w-full border p-2 rounded" required>
        <option value="">- Pilih Role -</option>
        <option value="Admin">Admin</option>
        <option value="WaliKelas">Wali Kelas</option>
        <option value="Siswa">Siswa</option>
      </select>
    </div>

    <!-- Fields for Siswa (will only show if role is "Siswa") -->
    <div id="siswa-fields" class="hidden">
      <div>
        <label class="font-medium">Nama Siswa</label>
        <input type="text" name="nama_siswa" class="w-full border p-2 rounded" required>
      </div>

      <div>
        <label class="font-medium">NISN</label>
        <!-- Membatasi panjang NISN hingga 20 karakter -->
        <input type="text" name="nisn" class="w-full border p-2 rounded" maxlength="10" required>
      </div>

      <div>
        <label class="font-medium">NIK</label>
        <!-- Membatasi panjang NISN hingga 20 karakter -->
        <input type="text" name="nik" class="w-full border p-2 rounded" maxlength="16" required>
      </div>

      <div>
        <label class="font-medium">Kelas</label>
        <input type="text" name="kelas" class="w-full border p-2 rounded" required>
      </div>

      <div>
        <label class="font-medium">Alamat</label>
        <input type="text" name="alamat" class="w-full border p-2 rounded" required>
      </div>
    </div>

    <button class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
      Simpan User
    </button>
  </form>
</div>

<script>
  // Script untuk menampilkan field siswa jika role Siswa dipilih
  document.querySelector('select[name="role"]').addEventListener('change', function() {
    const siswaFields = document.getElementById('siswa-fields');
    if (this.value === 'Siswa') {
      siswaFields.classList.remove('hidden');
    } else {
      siswaFields.classList.add('hidden');
    }
  });
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
