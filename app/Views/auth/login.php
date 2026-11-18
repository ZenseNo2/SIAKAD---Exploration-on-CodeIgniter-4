<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk · SIAKAD</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?= view('partials/sprite') ?>

<main class="auth-page">
  <div class="auth-card">
    <div class="auth-mark"><?= icon('cap') ?></div>
    <h1>SIAKAD</h1>
    <p class="subtitle">Masuk dengan NIM, NIDN, atau username admin.</p>

    <?= view('partials/flash') ?>

    <form action="<?= site_url('login') ?>" method="post" style="margin-top:16px">
      <?= csrf_field() ?>
      <div class="group form-group">
        <div class="row stack <?= has_error('username') ?>">
          <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" value="<?= esc(old('username')) ?>" placeholder="NIM / NIDN" autocomplete="username" autocapitalize="off" autofocus required>
          </div>
          <?= field_error('username') ?>
        </div>
        <div class="row stack <?= has_error('password') ?>">
          <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Wajib" autocomplete="current-password" required>
          </div>
          <?= field_error('password') ?>
        </div>
      </div>
      <button class="btn lg block" type="submit" style="margin-top:18px">Masuk</button>
    </form>

    <div class="demo">
      <div class="section-head"><h2>Akun Demo</h2></div>
      <div class="group">
        <div class="row"><span class="grow">Admin</span><code>admin</code> <code>admin123</code></div>
        <div class="row"><span class="grow">Dosen</span><code>0712058801</code></div>
        <div class="row"><span class="grow">Mahasiswa</span><code>25110001</code></div>
      </div>
      <p class="section-foot">Password awal dosen dan mahasiswa sama dengan username.</p>
    </div>
  </div>
</main>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
