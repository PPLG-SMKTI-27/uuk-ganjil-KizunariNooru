<?php ob_start(); ?>

<div class="bg-white p-6 rounded shadow">

  <!-- HEADER -->
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-semibold">Manajemen User</h2>
    <a href="<?= BASE_URL ?>?c=admin&m=createUser" 
       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
      + Tambah User
    </a>
  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto">
    <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Email</th>
          <th class="p-3 text-left">Role</th>
          <th class="p-3 text-center">Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php while($row = $users->fetch_assoc()): ?>
        <tr class="border-t hover:bg-gray-50">
          <td class="p-3"><?= $row['id_user'] ?></td>
          <td class="p-3"><?= htmlspecialchars($row['email']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['role']) ?></td>

          <td class="p-3 flex gap-3 justify-center">

            <!-- EDIT tombol -->
            <a href="<?= BASE_URL ?>?c=admin&m=editUser&id=<?= $row['id_user'] ?>"
               class="text-blue-600 hover:underline">
               Edit
            </a>

            <!-- DELETE tombol -->
            <a href="<?= BASE_URL ?>?c=admin&m=deleteUser&id=<?= $row['id_user'] ?>"
               class="text-red-600 hover:underline"
               onclick="return confirm('Hapus user ini?')">
               Hapus
            </a>

          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php 
$content = ob_get_clean(); 
include __DIR__ . '/../layout.php';
?>
