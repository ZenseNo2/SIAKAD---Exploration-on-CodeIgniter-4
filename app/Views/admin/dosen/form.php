<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/dosen') ?>"><?= icon('chevron-left') ?>Dosen</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form method="post" action="<?= site_url($row ? "admin/dosen/{$row['id']}" : 'admin/dosen') ?>">
  <?= csrf_field() ?>
  <section class="section">
    <div class="group form-group">
      <?= field_row('nidn', 'NIDN', '<input id="nidn" name="nidn" inputmode="numeric" value="' . esc(field_value('nidn', $row)) . '" placeholder="10 digit" required maxlength="20" autocomplete="off">') ?>
      <?= field_row('nama', 'Nama & gelar', '<input id="nama" name="nama" value="' . esc(field_value('nama', $row)) . '" placeholder="mis. Dewi Lestari, M.Kom." required maxlength="100">') ?>
      <?= field_row('email', 'Email', '<input id="email" type="email" name="email" value="' . esc(field_value('email', $row)) . '" placeholder="Opsional">') ?>
      <?= field_row('prodi_id', 'Homebase', '<select id="prodi_id" name="prodi_id">' . select_options($prodi, field_value('prodi_id', $row), 'Tidak ada') . '</select>') ?>
    </div>
    <?php if (! $row): ?>
      <p class="section-foot">Akun login dibuat otomatis dengan username dan password awal sama dengan NIDN.</p>
    <?php endif ?>
  </section>

  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/dosen') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>

<?php if ($row): ?>
  <section class="section">
    <div class="group">
      <form action="<?= site_url("admin/dosen/{$row['id']}/reset-password") ?>" method="post"
            data-confirm="Reset password?" data-confirm-text="Password akan dikembalikan menjadi NIDN." data-confirm-ok="Reset">
        <?= csrf_field() ?>
        <button class="row center link-btn" type="submit" style="width:100%">Reset password ke NIDN</button>
      </form>
    </div>
  </section>
<?php endif ?>
<?= $this->endSection() ?>
