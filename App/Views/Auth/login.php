<?php ob_start(); ?>
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-semibold mb-4">Login</h2>
  <?php if(isset($error)): ?><p class="text-red-600 mb-2"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form action="<?= BASE_URL ?>index.php?c=auth&m=proses" method="post" class="space-y-3">
    <input name="email" type="email" placeholder="Email" class="w-full p-2 border rounded" required>
    <input name="password" type="password" placeholder="Password" class="w-full p-2 border rounded" required>
    <button class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
  </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
