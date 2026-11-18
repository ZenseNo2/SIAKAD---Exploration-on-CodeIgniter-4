<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/mata-kuliah') ?>"><?= icon('chevron-left') ?>Mata Kuliah</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form method="post" action="<?= site_url($row ? "admin/mata-kuliah/{$row['id']}" : 'admin/mata-kuliah') ?>">
  <?= csrf_field() ?>
  <section class="section">
    <div class="group form-group">
      <?= field_row('kode', 'Kode', '<input id="kode" name="kode" value="' . esc(field_value('kode', $row)) . '" placeholder="mis. IF306" required maxlength="15">') ?>
      <?= field_row('nama', 'Nama', '<input id="nama" name="nama" value="' . esc(field_value('nama', $row)) . '" placeholder="Wajib" required maxlength="100">') ?>
      <?= field_row('sks', 'SKS', '<input id="sks" type="number" name="sks" value="' . esc(field_value('sks', $row, 3)) . '" min="1" max="6" required>') ?>
      <?= field_row('semester', 'Semester', '<input id="semester" type="number" name="semester" value="' . esc(field_value('semester', $row, 1)) . '" min="1" max="8" required>') ?>
      <?= field_row('prodi_id', 'Program studi', '<select id="prodi_id" name="prodi_id" required>' . select_options($prodi, field_value('prodi_id', $row), 'Pilih…') . '</select>') ?>
    </div>
  </section>
  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/mata-kuliah') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>
<?= $this->endSection() ?>
