<?php ob_start(); ?>
<div class="bg-white p-4 rounded shadow max-w-lg mx-auto">
  <h2 class="text-lg font-semibold mb-3">Ajukan Izin</h2>
  <form action="<?= BASE_URL ?>?c=siswa&m=store" method="post" class="space-y-3">
    <div><label class="text-sm">Keperluan</label><input name="keperluan" class="w-full p-2 border rounded" required></div>
    <div><label class="text-sm">Rencana Keluar</label><input name="keluar" type="datetime-local" class="w-full p-2 border rounded" required></div>
    <div><label class="text-sm">Rencana Kembali</label><input name="kembali" type="datetime-local" class="w-full p-2 border rounded" required></div>
    <div class="flex gap-2"><button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button><a href="<?= BASE_URL ?>?c=siswa&m=izin" class="px-4 py-2 border rounded">Batal</a></div>
  </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
