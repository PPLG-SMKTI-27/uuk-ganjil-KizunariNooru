<?php
/**
 * Form Ajukan Izin - Siswa View
 * Form untuk mengajukan izin baru
 */

require_once __DIR__ . '/../../Config/bootstrap.php';
require_once __DIR__ . '/../../Middleware/Auth.php';
require_once __DIR__ . '/../../Models/Siswa.php';

Guard::requireRole('Siswa');

$user = Auth::user();
$siswaModel = new Siswa();
$siswa = $siswaModel->findByUserId($user['id_user']);

$errors = $_SESSION['form_errors'] ?? [];
if (isset($_SESSION['form_errors'])) unset($_SESSION['form_errors']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajukan Izin - SISWA IZIN SYSTEM</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-brand">
                <div class="logo">SIS</div>
                <div class="header-info">
                    <h1>Ajukan Izin</h1>
                    <p>Formulir pengajuan izin sekolah</p>
                </div>
            </div>
            <div class="header-user">
                <a href="index.php?action=siswa.dashboard" class="btn btn-ghost btn-sm">← Kembali</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid">
            <div style="max-width: 600px;">
                <div class="card">
                    <!-- Form -->
                    <form method="POST" id="form-izin">
                        <!-- CSRF Token -->
                        <?= Csrf::field() ?>

                        <!-- Flash Messages -->
                        <?php if (isset($_SESSION['flash'])): ?>
                            <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
                            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> mb-2">
                                <?= htmlspecialchars($flash['msg']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Jenis Izin -->
                        <div class="form-group">
                            <label for="jenis_izin">Jenis Izin</label>
                            <select id="jenis_izin" name="jenis_izin" class="<?= isset($errors['jenis_izin']) ? 'form-error' : '' ?>">
                                <option value="">-- Pilih Jenis Izin --</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Keluarga">Keluarga</option>
                                <option value="Keperluan Penting">Keperluan Penting</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <?php if (isset($errors['jenis_izin'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['jenis_izin']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Alasan/Keperluan -->
                        <div class="form-group">
                            <label for="keperluan">Alasan Izin *</label>
                            <textarea
                                id="keperluan"
                                name="keperluan"
                                placeholder="Tuliskan alasan/keperluan izin Anda..."
                                class="<?= isset($errors['keperluan']) ? 'form-error' : '' ?>"
                                required
                            ></textarea>
                            <small>Minimal 10 karakter</small>
                            <?php if (isset($errors['keperluan'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['keperluan']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="form-group">
                            <label for="rencana_keluar">Tanggal Mulai *</label>
                            <input
                                type="date"
                                id="rencana_keluar"
                                name="rencana_keluar"
                                class="<?= isset($errors['rencana_keluar']) ? 'form-error' : '' ?>"
                                required
                            />
                            <?php if (isset($errors['rencana_keluar'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['rencana_keluar']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Akhir -->
                        <div class="form-group">
                            <label for="rencana_kembali">Tanggal Akhir *</label>
                            <input
                                type="date"
                                id="rencana_kembali"
                                name="rencana_kembali"
                                class="<?= isset($errors['rencana_kembali']) ? 'form-error' : '' ?>"
                                required
                            />
                            <?php if (isset($errors['rencana_kembali'])): ?>
                                <span class="error-message"><?= htmlspecialchars($errors['rencana_kembali']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div class="form-group">
                            <label for="catatan">Catatan Tambahan (Opsional)</label>
                            <textarea
                                id="catatan"
                                name="catatan"
                                placeholder="Informasi tambahan yang ingin disampaikan..."
                                style="min-height: 80px;"
                            ></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="btn-group" style="margin-top: 24px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                Kirim Permohonan
                            </button>
                            <a href="index.php?action=siswa.dashboard" class="btn btn-secondary" style="flex: 1; text-align: center;">
                                Batal
                            </a>
                        </div>
                    </form>

                    <!-- Info Card -->
                    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--gray-300); font-size: 13px; color: var(--gray-500);">
                        <h4 style="margin-bottom: 8px; color: var(--navy);">Informasi Penting:</h4>
                        <ul style="padding-left: 20px; line-height: 1.8;">
                            <li>Permohonan izin akan ditinjau oleh wali kelas</li>
                            <li>Waktu respons: maksimal 24 jam</li>
                            <li>Berikan alasan yang jelas dan lengkap</li>
                            <li>Tanggal akhir minimal sama dengan tanggal mulai</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div style="max-width: 300px;">
                <div class="card">
                    <h3 class="mb-2">Data Siswa</h3>
                    <div style="font-size: 13px;">
                        <div class="mb-1">
                            <strong>Nama:</strong><br><?= htmlspecialchars($siswa['nama_siswa']) ?>
                        </div>
                        <div class="mb-1">
                            <strong>Kelas:</strong><br><?= htmlspecialchars($siswa['kelas']) ?>
                        </div>
                        <div class="mb-1">
                            <strong>NISN:</strong><br><?= htmlspecialchars($siswa['nisn']) ?>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <h3 class="mb-2">Jenis Izin</h3>
                    <div style="font-size: 12px; color: var(--gray-500); line-height: 1.8;">
                        <div><strong>Sakit:</strong> Izin karena sakit</div>
                        <div><strong>Keluarga:</strong> Acara keluarga/keperluan keluarga</div>
                        <div><strong>Keperluan Penting:</strong> Urusan yang sangat mendesak</div>
                        <div><strong>Lainnya:</strong> Jenis izin lain yang tidak tercantum</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('form-izin').addEventListener('submit', function(e) {
            const keperluan = document.getElementById('keperluan').value.trim();
            const keluar = document.getElementById('rencana_keluar').value;
            const kembali = document.getElementById('rencana_kembali').value;

            // Validate keperluan length
            if (keperluan.length < 10) {
                e.preventDefault();
                alert('Alasan izin minimal 10 karakter');
                return;
            }

            // Validate dates
            if (keluar && kembali) {
                const tanggalKeluar = new Date(keluar);
                const tanggalKembali = new Date(kembali);

                if (tanggalKembali < tanggalKeluar) {
                    e.preventDefault();
                    alert('Tanggal akhir tidak boleh lebih awal dari tanggal mulai');
                    return;
                }
            }

            // Set min date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('rencana_keluar').min = today;
        });

        // Set minimum date to today on page load
        window.addEventListener('load', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('rencana_keluar').min = today;
            document.getElementById('rencana_kembali').min = today;

            // Update rencana_kembali minimum when rencana_keluar changes
            document.getElementById('rencana_keluar').addEventListener('change', function() {
                document.getElementById('rencana_kembali').min = this.value;
            });
        });
    </script>
</body>
</html>
