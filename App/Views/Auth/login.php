<?php
/**
 * Login View
 * Halaman login dengan design profesional responsive
 */
// Do not require login on the login page
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - SISWA IZIN SYSTEM</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/output.css">
  <style>
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0A1A44 0%, #1E3A8A 100%);
    }

    .login-card {
      background: var(--white);
      border-radius: 12px;
      box-shadow: var(--shadow-lg);
      width: 100%;
      max-width: 420px;
      padding: 32px;
    }

    .login-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .login-logo {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, var(--navy), #1E3A8A);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-weight: 700;
      font-size: 24px;
      margin: 0 auto 16px;
      box-shadow: var(--shadow);
    }

    .login-logo-text {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--navy);
    }

    .login-subtitle {
      font-size: 13px;
      color: var(--gray-500);
    }

    .login-divider {
      height: 1px;
      background-color: var(--gray-300);
      margin: 20px 0;
    }

    .form-group label {
      font-size: 13px;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      font-size: 15px;
    }

    @media (max-width: 480px) {
      .login-card {
        margin: 16px;
      }
    }
  </style>
</head>
<body style="background: linear-gradient(135deg, #0A1A44 0%, #1E3A8A 100%);">
  <div class="login-container">
    <div class="login-card fade-in">
      <!-- Header -->
      <div class="login-header">
        <div class="login-logo">SIS</div>
        <div class="login-logo-text">SISWA IZIN SYSTEM</div>
        <div class="login-subtitle">Manajemen Izin Sekolah</div>
      </div>

      <div class="login-divider"></div>

      <!-- Form -->
      <form method="POST" action="<?= BASE_URL ?>index.php?action=auth.processLogin" id="login-form">
        <!-- CSRF Token -->
        <?= Csrf::field() ?>

        <!-- Alert Errors -->
        <?php if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])): ?>
          <div class="alert alert-danger mb-2">
            <strong>Login Gagal</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
              <?php foreach ($_SESSION['login_errors'] as $error): ?>
                <li style="font-size: 13px;"><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
            <?php unset($_SESSION['login_errors']); ?>
        <?php endif; ?>

        <!-- Email or NIS Field -->
        <div class="form-group">
          <label for="email">Email atau NIS</label>
          <input
            type="text"
            id="email"
            name="email"
            placeholder="email@sekolah.or.id atau NIS"
            value="<?= htmlspecialchars($_SESSION['login_email'] ?? '') ?>"
            required
          />
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label for="password">Kata Sandi</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan kata sandi Anda"
            required
          />
        </div>

        <!-- Remember Me (Optional) -->
        <div class="form-group" style="flex-direction: row; align-items: center;">
          <input type="checkbox" id="remember" name="remember" style="width: auto; margin-right: 8px;">
          <label for="remember" style="margin-bottom: 0;">Ingat saya</label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-login mt-2">
          Masuk
        </button>
      </form>

      <!-- Footer Info -->
      <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--gray-300); text-align: center; font-size: 12px; color: var(--gray-500);">
        Gunakan akun sekolah Anda untuk login. Hubungi admin jika lupa password.
      </div>
    </div>
  </div>

  <script>
    // Validasi client-side: accept email or numeric NIS
    document.getElementById('login-form').addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      if (!email) {
        e.preventDefault();
        alert('Email atau NIS harus diisi');
        return;
      }

      if (!password) {
        e.preventDefault();
        alert('Password harus diisi');
        return;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const nisRegex = /^\d{4,}$/; // simple NIS check (at least 4 digits)

      if (!emailRegex.test(email) && !nisRegex.test(email)) {
        e.preventDefault();
        alert('Masukkan email yang valid atau NIS (angka)');
        return;
      }
    });

    // Clear old login errors
    <?php if (isset($_SESSION['login_errors'])) { unset($_SESSION['login_email']); } ?>
  </script>
</body>
</html>
