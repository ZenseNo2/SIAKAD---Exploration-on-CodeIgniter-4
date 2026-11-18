<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="group has-avatars">
    <div class="row">
      <span class="avatar"><?= esc(inisial(current_user('nama'))) ?></span>
      <span class="grow"><span class="title"><?= esc(current_user('nama')) ?></span><span class="sub"><?= role_label(current_user('role')) ?> · <?= esc(current_user('username')) ?></span></span>
    </div>
  </div>
</section>

<form method="post" action="<?= site_url('akun/password') ?>">
  <?= csrf_field() ?>
  <section class="section">
    <div class="section-head"><h2>Ganti Password</h2></div>
    <div class="group form-group">
      <?= field_row('password_lama', 'Password lama', '<input id="password_lama" type="password" name="password_lama" autocomplete="current-password" required>') ?>
      <?= field_row('password_baru', 'Password baru', '<input id="password_baru" type="password" name="password_baru" autocomplete="new-password" minlength="6" placeholder="Minimal 6 karakter" required>') ?>
      <?= field_row('konfirmasi_password', 'Ulangi', '<input id="konfirmasi_password" type="password" name="konfirmasi_password" autocomplete="new-password" required>') ?>
    </div>
    <p class="section-foot">Password awal dosen dan mahasiswa sama dengan NIDN/NIM. Sebaiknya segera diganti.</p>
  </section>
  <div class="form-actions"><button class="btn" type="submit">Simpan Password</button></div>
</form>
<?= $this->endSection() ?>
