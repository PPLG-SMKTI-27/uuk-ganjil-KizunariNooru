<?php ob_start(); ?>

<div class="bg-white p-6 rounded-lg shadow-lg">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold text-gray-800">Daftar Izin Saya</h2>
    <a href="<?= BASE_URL ?>index.php?action=siswa.create" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300">
      + Tambah Izin
    </a>
  </div>

  <!-- Tabel Daftar Izin -->  
  <div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="w-full text-left table-auto">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3 text-sm text-gray-600">No</th>
          <th class="p-3 text-sm text-gray-600">Keperluan</th>
          <th class="p-3 text-sm text-gray-600">Keluar</th>
          <th class="p-3 text-sm text-gray-600">Kembali</th>
          <th class="p-3 text-sm text-gray-600">Status</th>
          <th class="p-3 text-sm text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; $data = $data ?? []; foreach($data as $d): ?>
        <tr class="border-t hover:bg-gray-50 transition-colors">
          <td class="p-3"><?= $no++ ?></td>
          <td class="p-3"><?= htmlspecialchars($d['keperluan']) ?></td>
          <td class="p-3"><?= htmlspecialchars($d['rencana_keluar']) ?></td>
          <td class="p-3"><?= htmlspecialchars($d['rencana_kembali']) ?></td>
          <td class="p-3">
            <?php if($d['status']=='pending'): ?>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pending</span>
            <?php elseif($d['status']=='diizinkan'): ?>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Diizinkan</span>
            <?php else: ?>
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Ditolak</span>
            <?php endif; ?>
          </td>
          <td class="p-3">
            <?php if($d['status']=='pending'): ?>
              <a href="<?= BASE_URL ?>?c=siswa&m=delete&id=<?= $d['id_izin'] ?>" class="text-red-600 hover:text-red-700" onclick="return confirm('Hapus izin ini?')">
                <svg class="w-5 h-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Hapus
              </a>
              <a href="<?= BASE_URL ?>?c=siswa&m=edit&id=<?= $d['id_izin'] ?>" class="text-blue-600 hover:text-blue-700 ml-2">
                <svg class="w-5 h-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
              </a>
            <?php else: ?> -
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
