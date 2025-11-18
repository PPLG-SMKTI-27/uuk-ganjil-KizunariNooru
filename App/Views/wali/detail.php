<?php ob_start(); ?>
<div class="bg-white p-6 rounded-lg shadow-lg max-w-7xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">Detail Izin Siswa</h2>

    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-4 p-3 
            <?php echo $_SESSION['flash']['type'] == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> 
            rounded">
            <?= $_SESSION['flash']['msg']; ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Menampilkan Detail Izin -->
    <?php if (isset($izinDetail) && $izinDetail): ?>
        <div class="space-y-4">
            <!-- Nama Siswa -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Nama Siswa:</label>
                <p class="text-lg font-semibold"><?= htmlspecialchars($izinDetail['nama_siswa'] ?? 'Data tidak tersedia') ?></p>
            </div>

            <!-- Keperluan -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Keperluan:</label>
                <p><?= htmlspecialchars($izinDetail['keperluan'] ?? 'Data tidak tersedia') ?></p>
            </div>

            <!-- Tanggal Keluar -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Tanggal Keluar:</label>
                <p>
                    <?= htmlspecialchars(date('d-m-Y H:i', strtotime($izinDetail['rencana_keluar'] ?? ''))) ?: 'Tanggal tidak valid' ?>
                </p>
            </div>

            <!-- Tanggal Kembali -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Tanggal Kembali:</label>
                <p>
                    <?= htmlspecialchars(date('d-m-Y H:i', strtotime($izinDetail['rencana_kembali'] ?? ''))) ?: 'Tanggal tidak valid' ?>
                </p>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Status:</label>
                <p class="font-semibold 
                    <?= $izinDetail['status'] == 'diizinkan' ? 'text-green-600' : 
                       ($izinDetail['status'] == 'ditolak' ? 'text-red-600' : 'text-yellow-600') ?>">
                    <?= ucfirst(htmlspecialchars($izinDetail['status'] ?? 'Belum diproses')) ?>
                </p>
            </div>

            <!-- Komentar Wali -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Komentar Wali:</label>
                <p><?= htmlspecialchars($izinDetail['komentar_wali'] ?? 'Belum ada komentar') ?></p>
            </div>

            <!-- Informasi Tambahan Siswa -->
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm mt-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Siswa</h3>
                
                <!-- Kelas -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">Kelas:</label>
                    <p class="text-sm"><?= htmlspecialchars($izinDetail['kelas'] ?? 'Data tidak tersedia') ?></p>
                </div>

                <!-- NISN -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">NISN:</label>
                    <p class="text-sm"><?= htmlspecialchars($izinDetail['nisn'] ?? 'Data tidak tersedia') ?></p>
                </div>

                <!-- NIK -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">NIK:</label>
                    <p class="text-sm"><?= htmlspecialchars($izinDetail['nik'] ?? 'Data tidak tersedia') ?></p>
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">Alamat:</label>
                    <p class="text-sm"><?= htmlspecialchars($izinDetail['alamat'] ?? 'Data tidak tersedia') ?></p>
                </div>
            </div>

            <!-- Form Update Status Izin -->
            <div class="mb-6 mt-6">
                <h3 class="text-xl font-semibold text-gray-800">Update Status Izin</h3>
                <form action="<?= BASE_URL ?>?c=wali&m=proses" method="POST">
                    <input type="hidden" name="id" value="<?= $izinDetail['id_izin'] ?>">

                    <label for="status" class="block font-medium text-gray-700">Status Izin:</label>
                    <select name="status" id="status" class="form-select mt-2 p-2 border border-gray-300 rounded-lg">
                        <option value="pending" <?= $izinDetail['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="diizinkan" <?= $izinDetail['status'] == 'diizinkan' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="ditolak" <?= $izinDetail['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>

                    <label for="komentar" class="block font-medium text-gray-700 mt-4">Komentar:</label>
                    <textarea name="komentar" id="komentar" rows="4" class="form-textarea mt-2 p-2 border border-gray-300 rounded-lg"><?= htmlspecialchars($izinDetail['komentar_wali'] ?? '') ?></textarea>

                    <button type="submit" class="mt-4 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Jika Data Tidak Ditemukan -->
        <p>Data izin tidak ditemukan.</p>
    <?php endif; ?>

    <!-- Tombol Kembali -->
    <div class="mt-4">
        <a href="<?= BASE_URL ?>?c=wali&m=index" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
            Kembali ke Daftar Izin
        </a>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
