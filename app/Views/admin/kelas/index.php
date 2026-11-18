<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/kelas/new' . ($taId ? '?ta=' . $taId : '')) ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form class="toolbar" method="get" action="<?= site_url('admin/kelas') ?>">
  <select class="select-pill" name="ta" data-autosubmit aria-label="Tahun akademik">
    <?php foreach ($daftarTa as $t): ?>
      <option value="<?= $t['id'] ?>" <?= (int) $t['id'] === $taId ? 'selected' : '' ?>><?= esc($t['tahun'] . ' ' . $t['semester']) ?><?= $t['is_active'] ? ' (aktif)' : '' ?></option>
    <?php endforeach ?>
  </select>
  <select class="select-pill" name="prodi" data-autosubmit aria-label="Program studi">
    <?= select_options($prodi, $prodiId, 'Semua prodi') ?>
  </select>
</form>

<?php
// Kelompokkan per hari agar terbaca seperti jadwal
$perHari = [];
foreach ($rows as $r) {
    $perHari[(int) $r['hari']][] = $r;
}
?>

<?php foreach ($perHari as $hari => $list): ?>
<section class="section">
  <div class="section-head"><h2><?= nama_hari($hari) ?></h2><span class="meta"><?= count($list) ?> kelas</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th style="width:110px">Waktu</th><th>Mata Kuliah</th><th class="hide-sm">Dosen</th><th class="hide-sm">Ruang</th><th style="width:120px">Peserta</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($list as $k):
          $persen = $k['kuota'] > 0 ? min(100, round($k['jumlah_peserta'] / $k['kuota'] * 100)) : 0; ?>
        <tr>
          <td class="nowrap"><?= jam($k['jam_mulai']) ?>–<?= jam($k['jam_selesai']) ?></td>
          <td><a href="<?= site_url('admin/kelas/' . $k['id']) ?>"><?= esc($k['mk_nama']) ?></a><span class="sub"><?= esc($k['kode']) ?> · Kelas <?= esc($k['nama_kelas']) ?> · <?= $k['sks'] ?> SKS</span></td>
          <td class="hide-sm"><?= esc($k['dosen_nama']) ?></td>
          <td class="hide-sm"><?= esc($k['ruang']) ?></td>
          <td>
            <div class="meter <?= $persen >= 100 ? 'full' : ($persen >= 80 ? 'warn' : '') ?>"><span style="width:<?= $persen ?>%"></span></div>
            <div class="meter-label"><?= $k['jumlah_peserta'] ?> / <?= $k['kuota'] ?></div>
          </td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url("admin/kelas/{$k['id']}/edit") ?>">Ubah</a>
            <form action="<?= site_url("admin/kelas/{$k['id']}/delete") ?>" method="post" data-confirm="Hapus kelas <?= esc($k['kode'] . ' ' . $k['nama_kelas'], 'attr') ?>?" data-confirm-text="Kelas yang sudah memiliki peserta tidak dapat dihapus.">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</section>
<?php endforeach ?>

<?php if (! $rows): ?>
  <section class="section"><div class="group"><div class="row empty">Belum ada kelas pada tahun akademik ini.</div></div></section>
<?php endif ?>
<?= $this->endSection() ?>
