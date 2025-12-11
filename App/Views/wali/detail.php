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
    <?php if (isset($izin) && $izin): ?>
        <div class="space-y-4">
            <!-- Nama Siswa -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Nama Siswa:</label>
                <p class="text-lg font-semibold"><?= htmlspecialchars($siswa['nama_siswa'] ?? 'Data tidak tersedia') ?></p>
            </div>

            <!-- Keperluan -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Keperluan:</label>
                <p><?= htmlspecialchars($izin['keperluan'] ?? 'Data tidak tersedia') ?></p>
            </div>

            <!-- Tanggal Keluar -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Tanggal Keluar:</label>
                <p>
                    <?= htmlspecialchars(date('d-m-Y H:i', strtotime($izin['rencana_keluar'] ?? ''))) ?: 'Tanggal tidak valid' ?>
                </p>
            </div>

            <!-- Tanggal Kembali -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Tanggal Kembali:</label>
                <p>
                    <?= htmlspecialchars(date('d-m-Y H:i', strtotime($izin['rencana_kembali'] ?? ''))) ?: 'Tanggal tidak valid' ?>
                </p>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="font-medium text-gray-700">Status:</label>
                <p class="font-semibold
                    <?= $izin['status'] == 'diizinkan' ? 'text-green-600' :
                       ($izin['status'] == 'ditolak' ? 'text-red-600' : 'text-yellow-600') ?>">
                    <?= ucfirst(htmlspecialchars($izin['status'] ?? 'Belum diproses')) ?>
                </p>
            </div>

            <!-- Informasi Tambahan Siswa -->
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm mt-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Siswa</h3>

                <!-- Kelas -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">Kelas:</label>
                    <p class="text-sm"><?= htmlspecialchars($siswa['kelas'] ?? 'Data tidak tersedia') ?></p>
                </div>

                <!-- NISN -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">NISN:</label>
                    <p class="text-sm"><?= htmlspecialchars($siswa['nisn'] ?? 'Data tidak tersedia') ?></p>
                </div>

                <!-- NIK -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">NIK:</label>
                    <p class="text-sm"><?= htmlspecialchars($siswa['nik'] ?? 'Data tidak tersedia') ?></p>
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <label class="font-medium text-gray-700">Alamat:</label>
                    <p class="text-sm"><?= htmlspecialchars($siswa['alamat'] ?? 'Data tidak tersedia') ?></p>
                </div>
            </div>

            <!-- Form Update Status Izin -->
            <?php if ($izin['status'] === 'pending'): ?>
            <div class="mb-6 mt-6">
                <h3 class="text-xl font-semibold text-gray-800">Update Status Izin</h3>
                <form action="<?= BASE_URL ?>index.php?action=wali.approve" method="POST">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($izin['id_izin']) ?>">

                    <label for="notes" class="block font-medium text-gray-700 mt-4">Komentar:</label>
                    <textarea name="notes" id="notes" rows="4" class="form-textarea mt-2 p-2 border border-gray-300 rounded-lg"></textarea>

                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit" name="action" value="approve" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                            Setujui
                        </button>
                        <button type="submit" name="action" value="reject" class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Jika Data Tidak Ditemukan -->
        <p>Data izin tidak ditemukan.</p>
    <?php endif; ?>

    <!-- Tombol Kembali -->
    <div class="mt-4">
        <a href="<?= BASE_URL ?>index.php?action=wali.index" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
            Kembali ke Daftar Izin
        </a>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
