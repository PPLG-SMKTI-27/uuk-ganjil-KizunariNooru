<?php
/**
 * Dashboard Siswa View
 * Halaman dashboard dengan ringkasan izin & menu
 */

// Get user & siswa data
$user = Auth::user();
$siswaModel = new Siswa();
$siswa = $siswaModel->findByUserId($user['id_user']);

if (!$siswa) {
    die('Data siswa tidak ditemukan');
}

// Get izin data
$izinModel = new Izin();
$izinList = $izinModel->getBySiswa($siswa['id_siswa']);
$stats = $izinModel->getStats();

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Siswa</h1>
                    <p class="text-gray-600">Selamat datang, <?= htmlspecialchars($siswa['nama_siswa']) ?></p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-semibold"><?= htmlspecialchars($siswa['nama_siswa']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($siswa['kelas']) ?></p>
                    </div>
                    <a href="index.php?action=auth.logout" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Keluar</a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-sm text-gray-500">Menunggu</h3>
                <p class="text-2xl font-bold text-yellow-600"><?= $stats['pending'] ?? 0 ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-sm text-gray-500">Disetujui</h3>
                <p class="text-2xl font-bold text-green-600"><?= $stats['diizinkan'] ?? 0 ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-sm text-gray-500">Ditolak</h3>
                <p class="text-2xl font-bold text-red-600"><?= $stats['ditolak'] ?? 0 ?></p>
            </div>
        </div>

        <!-- Menu Cepat -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h3 class="text-lg font-semibold mb-4">Menu Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="index.php?action=siswa.create" class="block p-4 bg-blue-500 text-white rounded hover:bg-blue-600 text-center">
                    + Ajukan Izin Baru
                </a>
                <a href="index.php?action=siswa.history" class="block p-4 bg-green-500 text-white rounded hover:bg-green-600 text-center">
                    📋 Riwayat Izin
                </a>
                <a href="index.php?action=siswa.profile" class="block p-4 bg-purple-500 text-white rounded hover:bg-purple-600 text-center">
                    👤 Profil Akun
                </a>
            </div>
        </div>

        <!-- Riwayat Izin Terbaru -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Riwayat Izin Terbaru</h3>
                <a href="index.php?action=siswa.history" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Lihat Semua</a>
            </div>

            <?php if (!empty($izinList)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-left">Alasan</th>
                                <th class="px-4 py-2 text-left">Rentang</th>
                                <th class="px-4 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($izinList, 0, 5) as $izin): ?>
                                <tr class="border-b">
                                    <td class="px-4 py-2"><?= date('d M Y', strtotime($izin['rencana_keluar'])) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars(substr($izin['keperluan'], 0, 20)) ?>...</td>
                                    <td class="px-4 py-2">
                                        <?= date('d M', strtotime($izin['rencana_keluar'])) ?> -
                                        <?= date('d M', strtotime($izin['rencana_kembali'])) ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php
                                        $statusColor = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'diizinkan' => 'bg-green-100 text-green-800',
                                            'ditolak' => 'bg-red-100 text-red-800'
                                        ];
                                        $status = $izin['status'];
                                        ?>
                                        <span class="px-2 py-1 rounded text-sm <?= $statusColor[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">Belum ada izin yang diajukan</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
