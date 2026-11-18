<?php

use App\Models\TahunAkademikModel;

?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= tanggal_id() ?> · Semester aktif <?= esc(TahunAkademikModel::label($ta)) ?>
  <?php if ($ta): ?><span class="badge <?= $ta['krs_dibuka'] ? 'green' : '' ?>"><?= $ta['krs_dibuka'] ? 'KRS dibuka' : 'KRS ditutup' ?></span><?php endif ?>
</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stats">
  <div class="stat"><div class="k"><?= icon('users') ?>Mahasiswa</div><div class="v"><?= $jumlah['mahasiswa'] ?></div><div class="d">terdaftar</div></div>
  <div class="stat"><div class="k"><?= icon('idcard') ?>Dosen</div><div class="v"><?= $jumlah['dosen'] ?></div><div class="d">pengajar</div></div>
  <div class="stat"><div class="k"><?= icon('book') ?>Mata Kuliah</div><div class="v"><?= $jumlah['mata_kuliah'] ?></div><div class="d">dalam kurikulum</div></div>
  <div class="stat"><div class="k"><?= icon('calendar') ?>Kelas</div><div class="v"><?= $jumlah['kelas'] ?></div><div class="d">dibuka semester ini</div></div>
</div>

<?php if ($ta && $belumKrs > 0): ?>
  <div class="alert info"><?= icon('info') ?><div class="grow"><b><?= $belumKrs ?> mahasiswa</b> belum mengisi KRS untuk <?= esc(TahunAkademikModel::label($ta)) ?>.</div></div>
<?php endif ?>

<section class="section">
  <div class="section-head"><h2>Kelas Semester Ini</h2><a href="<?= site_url('admin/kelas') ?>">Lihat semua</a></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Mata Kuliah</th><th class="hide-sm">Dosen</th><th>Jadwal</th><th style="width:150px">Peserta</th></tr></thead>
      <tbody>
      <?php foreach ($kelas as $k):
          $persen = $k['kuota'] > 0 ? min(100, round($k['jumlah_peserta'] / $k['kuota'] * 100)) : 0; ?>
        <tr>
          <td><a href="<?= site_url('admin/kelas/' . $k['id']) ?>"><?= esc($k['mk_nama']) ?></a><span class="sub"><?= esc($k['kode']) ?> · Kelas <?= esc($k['nama_kelas']) ?> · <?= $k['sks'] ?> SKS</span></td>
          <td class="hide-sm"><?= esc($k['dosen_nama']) ?></td>
          <td class="nowrap"><?= nama_hari((int) $k['hari']) ?><span class="sub"><?= jam($k['jam_mulai']) ?>–<?= jam($k['jam_selesai']) ?> · <?= esc($k['ruang']) ?></span></td>
          <td>
            <div class="meter <?= $persen >= 100 ? 'full' : ($persen >= 80 ? 'warn' : '') ?>"><span style="width:<?= $persen ?>%"></span></div>
            <div class="meter-label"><?= $k['jumlah_peserta'] ?> / <?= $k['kuota'] ?></div>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $kelas): ?>
        <tr><td colspan="4" class="c muted" style="padding:22px">Belum ada kelas untuk semester aktif.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Pintasan</h2></div>
  <div class="group has-icons">
    <a class="row" href="<?= site_url('admin/mahasiswa/new') ?>"><span class="tile t-green"><?= icon('plus') ?></span><span class="grow">Tambah mahasiswa</span><?= icon('chevron-right', 'chev') ?></a>
    <a class="row" href="<?= site_url('admin/kelas/new') ?>"><span class="tile t-blue"><?= icon('plus') ?></span><span class="grow">Buka kelas baru</span><?= icon('chevron-right', 'chev') ?></a>
    <a class="row" href="<?= site_url('admin/tahun-akademik') ?>"><span class="tile t-red"><?= icon('clock') ?></span><span class="grow">Atur semester & periode KRS</span><?= icon('chevron-right', 'chev') ?></a>
  </div>
</section>
<?= $this->endSection() ?>
