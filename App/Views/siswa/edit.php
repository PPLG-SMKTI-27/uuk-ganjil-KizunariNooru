<?php ob_start(); ?>
<div class="bg-white p-4 rounded shadow max-w-lg mx-auto">
  <h2 class="text-lg font-semibold mb-3">Edit Izin</h2>
  <form action="<?= BASE_URL ?>index.php?action=siswa.update" method="post" class="space-y-3">
    <?= Csrf::field() ?>
    <input type="hidden" name="id_izin" value="<?= htmlspecialchars($izinToEdit['id_izin'] ?? '') ?>">
    <div>
      <label class="text-sm">Keperluan</label>
      <input name="keperluan" value="<?= htmlspecialchars($izinToEdit['keperluan'] ?? '') ?>" class="w-full p-2 border rounded" required>
    </div>
    <div>
      <label class="text-sm">Rencana Keluar</label>
      <input name="rencana_keluar" type="datetime-local" value="<?= date('Y-m-d\TH:i', strtotime($izinToEdit['rencana_keluar'] ?? '')) ?>" class="w-full p-2 border rounded" required>
    </div>
    <div>
      <label class="text-sm">Rencana Kembali</label>
      <input name="rencana_kembali" type="datetime-local" value="<?= date('Y-m-d\TH:i', strtotime($izinToEdit['rencana_kembali'] ?? '')) ?>" class="w-full p-2 border rounded" required>
    </div>
    <div class="flex gap-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
      <a href="<?= BASE_URL ?>index.php?action=siswa.history" class="px-4 py-2 border rounded">Batal</a>
    </div>
  </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
