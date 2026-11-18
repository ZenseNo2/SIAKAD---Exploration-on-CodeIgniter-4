<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/kelas') ?>"><?= icon('chevron-left') ?>Jadwal</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$editId = $baru ? null : ($row['id'] ?? null);
$taOptions = [];
foreach ($daftarTa as $t) {
    $taOptions[$t['id']] = $t['tahun'] . ' ' . $t['semester'] . ($t['is_active'] ? ' (aktif)' : '');
}
$hari = config('Akademik')->hari;
?>
<form method="post" action="<?= site_url($editId ? "admin/kelas/{$editId}" : 'admin/kelas') ?>">
  <?= csrf_field() ?>

  <section class="section">
    <div class="section-head"><h2>Mata Kuliah</h2></div>
    <div class="group form-group">
      <?= field_row('tahun_akademik_id', 'Tahun akademik', '<select id="tahun_akademik_id" name="tahun_akademik_id" required>' . select_options($taOptions, field_value('tahun_akademik_id', $row)) . '</select>') ?>
      <?= field_row('mata_kuliah_id', 'Mata kuliah', '<select id="mata_kuliah_id" name="mata_kuliah_id" required>' . select_options($mataKuliah, field_value('mata_kuliah_id', $row), 'Pilih…') . '</select>') ?>
      <?= field_row('nama_kelas', 'Nama kelas', '<input id="nama_kelas" name="nama_kelas" value="' . esc(field_value('nama_kelas', $row, 'A')) . '" maxlength="5" required>') ?>
      <?= field_row('dosen_id', 'Dosen pengampu', '<select id="dosen_id" name="dosen_id" required>' . select_options($dosen, field_value('dosen_id', $row), 'Pilih…') . '</select>') ?>
    </div>
  </section>

  <section class="section">
    <div class="section-head"><h2>Jadwal</h2></div>
    <div class="group form-group">
      <?= field_row('hari', 'Hari', '<select id="hari" name="hari" required>' . select_options($hari, field_value('hari', $row, 1)) . '</select>') ?>
      <?= field_row('jam_mulai', 'Mulai', '<input id="jam_mulai" type="time" name="jam_mulai" value="' . esc(substr(field_value('jam_mulai', $row, '07:30'), 0, 5)) . '" required>') ?>
      <?= field_row('jam_selesai', 'Selesai', '<input id="jam_selesai" type="time" name="jam_selesai" value="' . esc(substr(field_value('jam_selesai', $row, '10:00'), 0, 5)) . '" required>') ?>
      <?= field_row('ruang', 'Ruang', '<input id="ruang" name="ruang" value="' . esc(field_value('ruang', $row)) . '" placeholder="mis. Lab 1" maxlength="20" required>') ?>
      <?= field_row('kuota', 'Kuota', '<input id="kuota" type="number" name="kuota" value="' . esc(field_value('kuota', $row, 40)) . '" min="1" max="200" required><span class="unit">mahasiswa</span>') ?>
    </div>
    <p class="section-foot">Sistem menolak jadwal yang bentrok dengan kelas lain pada dosen atau ruang yang sama.</p>
  </section>

  <div class="form-actions">
    <a class="btn gray" href="<?= site_url('admin/kelas') ?>">Batal</a>
    <button class="btn" type="submit">Simpan</button>
  </div>
</form>
<?= $this->endSection() ?>
