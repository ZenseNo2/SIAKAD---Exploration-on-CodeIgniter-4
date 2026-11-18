<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/mahasiswa') ?>"><?= icon('chevron-left') ?>Mahasiswa</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form method="post" action="<?= site_url($row && isset($row['id']) ? "admin/mahasiswa/{$row['id']}" : 'admin/mahasiswa') ?>">
  <?= csrf_field() ?>

  <section class="section">
    <div class="section-head"><h2>Identitas</h2></div>
    <div class="group form-group">
      <?= field_row('nim', 'NIM', '<input id="nim" name="nim" value="' . esc(field_value('nim', $row)) . '" placeholder="mis. 25110008" required maxlength="20" autocomplete="off">') ?>
      <?= field_row('nama', 'Nama lengkap', '<input id="nama" name="nama" value="' . esc(field_value('nama', $row)) . '" placeholder="Wajib" required maxlength="100">') ?>
      <div class="row stack <?= has_error('jenis_kelamin') ?>">
        <div class="field">
          <label>Jenis kelamin</label>
          <?php $jk = field_value('jenis_kelamin', $row, 'L'); ?>
          <div class="segmented" role="radiogroup">
            <input type="radio" id="jk-l" name="jenis_kelamin" value="L" <?= $jk === 'L' ? 'checked' : '' ?>><label for="jk-l">Laki-laki</label>
            <input type="radio" id="jk-p" name="jenis_kelamin" value="P" <?= $jk === 'P' ? 'checked' : '' ?>><label for="jk-p">Perempuan</label>
          </div>
        </div>
        <?= field_error('jenis_kelamin') ?>
      </div>
      <?= field_row('email', 'Email', '<input id="email" type="email" name="email" value="' . esc(field_value('email', $row)) . '" placeholder="Opsional">') ?>
    </div>
  </section>

  <section class="section">
    <div class="section-head"><h2>Akademik</h2></div>
    <div class="group form-group">
      <?= field_row('prodi_id', 'Program studi', '<select id="prodi_id" name="prodi_id" required>' . select_options($prodi, field_value('prodi_id', $row), 'Pilih…') . '</select>') ?>
      <?= field_row('angkatan', 'Angkatan', '<input id="angkatan" type="number" name="angkatan" value="' . esc(field_value('angkatan', $row, date('Y'))) . '" min="2000" max="' . (date('Y') + 1) . '" required>') ?>
      <?= field_row('dosen_wali_id', 'Dosen wali', '<select id="dosen_wali_id" name="dosen_wali_id">' . select_options($dosen, field_value('dosen_wali_id', $row), 'Belum ditentukan') . '</select>') ?>
    </div>
    <?php if (! $row): ?>
      <p class="section-foot">Akun login dibuat otomatis dengan username dan password awal sama dengan NIM.</p>
    <?php endif ?>
  </section>

  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/mahasiswa') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>

<?php if ($row): ?>
  <section class="section">
    <div class="group">
      <form action="<?= site_url("admin/mahasiswa/{$row['id']}/reset-password") ?>" method="post"
            data-confirm="Reset password?" data-confirm-text="Password akan dikembalikan menjadi NIM." data-confirm-ok="Reset">
        <?= csrf_field() ?>
        <button class="row center link-btn" type="submit" style="width:100%">Reset password ke NIM</button>
      </form>
    </div>
  </section>
<?php endif ?>
<?= $this->endSection() ?>
