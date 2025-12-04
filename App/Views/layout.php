<?php
// Start session if not already started
if (!isset($_SESSION)) {
    session_start();
}

// Retrieve and clear flash message from session
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perizinan Siswa</title>
    <!-- Link to compiled Tailwind CSS -->
    <link href="<?= BASE_URL ?>css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <!-- Main Logo/Title -->
                <a href="<?= BASE_URL ?>" class="font-bold text-lg text-gray-800">Sistem Perizinan</a>

                <!-- Role-based Navigation Links -->
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($_SESSION['user']['role'] === 'Siswa'): ?>
                        <a href="<?= BASE_URL ?>?c=siswa&m=dashboard" class="text-sm text-gray-600 hover:text-gray-800">Dashboard</a>
                        <a href="<?= BASE_URL ?>?c=siswa&m=index" class="text-sm text-gray-600 hover:text-gray-800">Izin Saya</a>
                    <?php elseif ($_SESSION['user']['role'] === 'WaliKelas'): ?>
                        <a href="<?= BASE_URL ?>?c=wali&m=index" class="text-sm text-gray-600 hover:text-gray-800">Daftar Izin</a>
                    <?php elseif ($_SESSION['user']['role'] === 'Admin'): ?>
                        <a href="<?= BASE_URL ?>?c=admin&m=index" class="text-sm text-gray-600 hover:text-gray-800">Manajemen User</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="flex items-center">
                <!-- User Info and Logout/Login -->
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="text-sm text-gray-700 mr-3">
                        <?= htmlspecialchars($_SESSION['user']['email']) ?>
                    </span>
                    <a href="<?= BASE_URL ?>?c=auth&m=logout" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>?c=auth&m=loginView" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="max-w-6xl mx-auto p-4">
        <!-- Flash Message Display -->
        <?php if ($flash): ?>
            <div class="mb-4">
                <div class="p-3 rounded <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                    <?= htmlspecialchars($flash['msg']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div>
            <?= $content ?? '' ?>
        </div>
    </div>
</body>
</html>
