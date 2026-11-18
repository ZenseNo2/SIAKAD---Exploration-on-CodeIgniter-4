<?php
/**
 * Layout utama setelah login.
 * View anak mengisi section: 'content' (wajib), 'actions' (tombol kanan navbar),
 * 'back' (tautan kembali kiri navbar), 'subtitle' (teks kecil di bawah judul besar).
 */
include APPPATH . 'Views/partials/menu.php';
$user = current_user();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f2f2f7" media="(prefers-color-scheme: light)">
  <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
  <title><?= esc($title ?? 'SIAKAD') ?> · SIAKAD</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?= view('partials/sprite') ?>

<div class="app">
  <!-- ================= Sidebar (seperti Settings di iPadOS) ================= -->
  <aside class="sidebar" aria-label="Navigasi">
    <div class="sidebar-brand"><b>SIAKAD</b><span>Sistem Informasi Akademik</span></div>

    <a class="profile-cell" href="<?= site_url('akun/password') ?>">
      <span class="avatar"><?= esc(inisial($user['nama'])) ?></span>
      <span class="who"><b><?= esc($user['nama']) ?></b><span><?= role_label($user['role']) ?> · <?= esc($user['username']) ?></span></span>
    </a>

    <?php foreach ($menu as $grup => $items): ?>
      <div class="nav-group-title"><?= esc($grup) ?></div>
      <nav class="nav-group">
        <?php foreach ($items as [$url, $label, $ic, $warna]): ?>
          <a class="nav-item <?= menu_active($url) ?>" href="<?= site_url($url) ?>">
            <span class="tile <?= $warna ?>"><?= icon($ic) ?></span>
            <span class="label"><?= esc($label) ?></span>
          </a>
        <?php endforeach ?>
      </nav>
    <?php endforeach ?>

    <form action="<?= site_url('logout') ?>" method="post">
      <?= csrf_field() ?>
      <button class="logout-btn" type="submit">Keluar</button>
    </form>
  </aside>

  <!-- ================= Konten ================= -->
  <div class="main">
    <header class="navbar">
      <div class="nav-left">
        <button class="menu-btn" type="button" data-open-nav aria-label="Buka menu"><?= icon('sidebar') ?></button>
        <?= $this->renderSection('back') ?>
      </div>
      <div class="nav-title"><?= esc($title ?? '') ?></div>
      <div class="nav-right"><?= $this->renderSection('actions') ?></div>
    </header>

    <main class="content">
      <h1 class="large-title"><?= esc($title ?? '') ?></h1>
      <?= $this->renderSection('subtitle') ?>
      <?= view('partials/flash') ?>
      <?= $this->renderSection('content') ?>
    </main>
  </div>
</div>

<!-- ================= Tab bar (layar kecil) ================= -->
<nav class="tabbar" aria-label="Menu utama">
  <?php foreach ($menu as $items): foreach ($items as [$url, $label, $ic, , $tab]): if (! $tab) continue; ?>
    <a class="<?= menu_active($url) ?>" href="<?= site_url($url) ?>"><?= icon($ic) ?><span><?= esc($label) ?></span></a>
  <?php endforeach; endforeach ?>
  <button type="button" data-open-nav><?= icon('sidebar') ?><span>Menu</span></button>
</nav>

<!-- ================= Dialog konfirmasi (UIAlertController) ================= -->
<dialog class="alert-dialog" id="confirm-dialog">
  <div class="body"><h3></h3><p></p></div>
  <div class="buttons">
    <button type="button" data-cancel>Batal</button>
    <button type="button" class="destructive" data-ok>Hapus</button>
  </div>
</dialog>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
