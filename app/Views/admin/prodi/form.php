<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/prodi') ?>"><?= icon('chevron-left') ?>Program Studi</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form method="post" action="<?= site_url($row ? "admin/prodi/{$row['id']}" : 'admin/prodi') ?>">
  <?= csrf_field() ?>
  <section class="section">
    <div class="group form-group">
      <?= field_row('kode', 'Kode', '<input id="kode" name="kode" value="' . esc(field_value('kode', $row)) . '" placeholder="mis. TI" required maxlength="10">') ?>
      <?= field_row('nama', 'Nama', '<input id="nama" name="nama" value="' . esc(field_value('nama', $row)) . '" placeholder="mis. Teknik Informatika" required maxlength="100">') ?>
      <?= field_row('jenjang', 'Jenjang', '<select id="jenjang" name="jenjang">' . select_options(['D3' => 'D3', 'D4' => 'D4', 'S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3'], field_value('jenjang', $row, 'S1')) . '</select>') ?>
    </div>
  </section>
  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/prodi') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>
<?= $this->endSection() ?>
