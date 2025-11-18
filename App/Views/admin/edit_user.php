<?php ob_start(); ?>

<div class="max-w-lg mx-auto bg-white p-6 rounded shadow space-y-4">
  <h2 class="text-2xl font-semibold mb-4">Edit User</h2>

  <form action="<?= BASE_URL ?>?c=admin&m=updateUser" method="post" class="space-y-3">
    
    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

    <div>
      <label>Nama</label>
      <input name="nama" value="<?= $user['nama'] ?? '' ?>" class="w-full border p-2 rounded">
    </div>

    <div>
      <label>Email</label>
      <input name="email" value="<?= $user['email'] ?>" required class="w-full border p-2 rounded">
    </div>

    <div>
      <label>Role</label>
      <select name="role" class="w-full border p-2 rounded">
        <option <?= $user['role']=='Admin'?'selected':'' ?> value="Admin">Admin</option>
        <option <?= $user['role']=='WaliKelas'?'selected':'' ?> value="WaliKelas">Wali Kelas</option>
        <option <?= $user['role']=='Siswa'?'selected':'' ?> value="Siswa">Siswa</option>
      </select>
    </div>

    <div>
      <label>Password Baru (opsional)</label>
      <input name="password" type="password" placeholder="Kosongkan jika tidak diganti"
             class="w-full border p-2 rounded">
    </div>

    <?php if($user['role'] === 'Siswa'): ?>
      <div>
        <label>Nama Siswa</label>
        <input name="nama_siswa" class="w-full border p-2 rounded"
               value="<?= $siswa['nama_siswa'] ?>">
      </div>

      <div>
        <label>Kelas</label>
        <input name="kelas" class="w-full border p-2 rounded"
               value="<?= $siswa['kelas'] ?>">
      </div>
    <?php endif; ?>

    <button class="w-full bg-blue-600 text-white p-2 rounded">
      Update
    </button>

  </form>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
