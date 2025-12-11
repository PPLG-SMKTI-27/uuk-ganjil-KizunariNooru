<?php ob_start(); ?>
<div class="bg-white p-6 rounded-lg shadow-lg max-w-7xl mx-auto">
  <!-- Filter Section -->
  <div class="flex flex-wrap gap-4 mb-6">
    <form method="get" action="<?= BASE_URL ?>index.php" class="flex flex-wrap gap-4 items-center w-full">
      <input type="hidden" name="action" value="wali.index">

      <!-- Dropdown Status -->
      <select name="status" class="p-2 border rounded-lg text-gray-700 w-48">
        <option value="">Semua</option>
        <option value="pending">Pending</option>
        <option value="diizinkan">Diizinkan</option>
        <option value="ditolak">Ditolak</option>
      </select>

      <!-- Input Cari Siswa -->
      <input name="q" placeholder="Cari nama siswa" class="p-2 border rounded-lg text-gray-700 w-64">

      <!-- Tombol Filter -->
      <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
        Filter
      </button>
    </form>
  </div>

  <!-- Tabel Data Siswa -->
  <div class="overflow-x-auto">
    <table class="w-full table-auto bg-white border-collapse shadow-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3 text-left text-sm font-medium text-gray-700">No</th>
          <th class="p-3 text-left text-sm font-medium text-gray-700">Nama</th>
          <th class="p-3 text-left text-sm font-medium text-gray-700">Keperluan</th>
          <th class="p-3 text-left text-sm font-medium text-gray-700">Keluar</th>
          <th class="p-3 text-left text-sm font-medium text-gray-700">Kembali</th>
          <th class="p-3 text-left text-sm font-medium text-gray-700">Status</th>
          <th class="p-3 text-center text-sm font-medium text-gray-700">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; $data = $data ?? []; foreach($data as $d): ?>
        <tr class="border-t border-gray-200 hover:bg-gray-50 transition duration-150">
          <td class="p-3 text-sm"><?= $no++ ?></td>
          <td class="p-3 text-sm"><?= htmlspecialchars($d['nama_siswa']) ?></td>
          <td class="p-3 text-sm"><?= htmlspecialchars($d['keperluan']) ?></td>
          <td class="p-3 text-sm"><?= htmlspecialchars($d['rencana_keluar']) ?></td>
          <td class="p-3 text-sm"><?= htmlspecialchars($d['rencana_kembali']) ?></td>
          <td class="p-3 text-sm">
            <span class="text-sm 
              <?= $d['status'] == 'diizinkan' ? 'text-green-600' : 
                 ($d['status'] == 'ditolak' ? 'text-red-600' : 'text-yellow-600') ?>
              font-semibold"><?= htmlspecialchars($d['status']) ?></span>
          </td>
          <td class="p-3 text-center flex justify-center gap-2 flex-wrap">
            <!-- Tombol Setujui dan Tolak -->
            <form method="post" action="<?= BASE_URL ?>index.php?action=wali.approve" class="flex gap-2">
              <?= Csrf::field() ?>
              <input type="hidden" name="id" value="<?= htmlspecialchars($d['id_izin']) ?>">
              <button type="submit" name="action" value="approve" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                Setujui
              </button>
              <button type="submit" name="action" value="reject" class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                Tolak
              </button>
            </form>
            <!-- Tombol Detail -->
            <a href="<?= BASE_URL ?>index.php?action=wali.detail&id=<?= htmlspecialchars($d['id_izin']) ?>"
               class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
              Lihat Detail
            </a>
            <!-- Tombol Hapus -->
            <a href="<?= BASE_URL ?>index.php?action=wali.delete&id=<?= htmlspecialchars($d['id_izin']) ?>"
               class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200"
               onclick="return confirm('Apakah Anda yakin ingin menghapus izin ini?')">
              Hapus
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
