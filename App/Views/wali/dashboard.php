<?php
/**
 * Wali Dashboard View
 * Halaman dashboard untuk wali kelas
 */

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Wali Kelas</h1>
                    <p class="text-gray-600">Kelola persetujuan izin siswa</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-semibold"><?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                        <p class="text-sm text-gray-500">Wali Kelas</p>
                    </div>
                    <a href="index.php?action=auth.logout" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Keluar</a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-sm text-gray-500">Menunggu Persetujuan</h3>
                <p class="text-2xl font-bold text-yellow-600"><?= $totalPending ?? 0 ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-sm text-gray-500">Disetujui</h3>
                <p class="text-2xl font-bold text-green-600"><?= $totalApproved ?? 0 ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-sm text-gray-500">Ditolak</h3>
                <p class="text-2xl font-bold text-red-600"><?= $totalRejected ?? 0 ?></p>
            </div>
        </div>

        <!-- Menu Cepat -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h3 class="text-lg font-semibold mb-4">Menu Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="index.php?action=wali.index" class="block p-4 bg-blue-500 text-white rounded hover:bg-blue-600 text-center">
                    📋 Daftar Izin
                </a>
                <a href="index.php?action=wali.index&status=pending" class="block p-4 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-center">
                    ⏳ Izin Pending
                </a>
                <a href="index.php?action=wali.stats" class="block p-4 bg-purple-500 text-white rounded hover:bg-purple-600 text-center">
                    📊 Statistik
                </a>
            </div>
        </div>

        <!-- Recent Pending Izin -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Izin Menunggu Persetujuan</h3>
                <a href="index.php?action=wali.index&status=pending" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Lihat Semua</a>
            </div>

            <?php if (!empty($pendingIzin)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Nama Siswa</th>
                                <th class="px-4 py-2 text-left">Kelas</th>
                                <th class="px-4 py-2 text-left">Alasan</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($pendingIzin, 0, 5) as $izin): ?>
                                <tr class="border-b">
                                    <td class="px-4 py-2"><?= htmlspecialchars($izin['nama_siswa']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($izin['kelas']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars(substr($izin['keperluan'], 0, 30)) ?>...</td>
                                    <td class="px-4 py-2">
                                        <?= date('d M Y', strtotime($izin['rencana_keluar'])) ?> -
                                        <?= date('d M Y', strtotime($izin['rencana_kembali'])) ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="index.php?action=wali.detail&id=<?= $izin['id_izin'] ?>" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">Tidak ada izin yang menunggu persetujuan</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
