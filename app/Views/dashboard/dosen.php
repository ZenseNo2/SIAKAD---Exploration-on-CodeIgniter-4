<?php

use App\Models\TahunAkademikModel;

?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= tanggal_id() ?> · Semester <?= esc(TahunAkademikModel::label($ta)) ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stats">
  <div class="stat"><div class="k"><?= icon('calendar') ?>Kelas Diampu</div><div class="v"><?= count($kelas) ?></div><div class="d">semester ini</div></div>
  <div class="stat"><div class="k"><?= icon('users') ?>Total Peserta</div><div class="v"><?= array_sum(array_column($kelas, 'jumlah_peserta')) ?></div><div class="d">mahasiswa</div></div>
  <div class="stat"><div class="k"><?= icon('person') ?>Perwalian</div><div class="v"><?= count($wali) ?></div><div class="d">mahasiswa bimbingan</div></div>
  <div class="stat"><div class="k"><?= icon('clock') ?>Hari Ini</div><div class="v"><?= count($jadwal) ?></div><div class="d">jadwal mengajar</div></div>
</div>

<section class="section">
  <div class="section-head"><h2>Jadwal Hari Ini</h2></div>
  <div class="group">
    <?php foreach ($jadwal as $k): ?>
      <a class="row" href="<?= site_url('dosen/kelas/' . $k['id']) ?>">
        <div class="slot grow">
          <div class="time"><b><?= jam($k['jam_mulai']) ?></b><?= jam($k['jam_selesai']) ?></div>
          <div class="info"><span class="title"><?= esc($k['mk_nama']) ?> · <?= esc($k['nama_kelas']) ?></span><span class="sub"><?= esc($k['ruang']) ?> · <?= $k['jumlah_peserta'] ?> peserta</span></div>
        </div>
        <?= icon('chevron-right', 'chev') ?>
      </a>
    <?php endforeach ?>
    <?php if (! $jadwal): ?><div class="row empty">Tidak ada jadwal mengajar hari ini.</div><?php endif ?>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Kelas Semester Ini</h2><a href="<?= site_url('dosen/kelas') ?>">Semua kelas</a></div>
  <div class="group">
    <?php foreach ($kelas as $k): ?>
      <a class="row" href="<?= site_url('dosen/kelas/' . $k['id']) ?>">
        <span class="grow"><span class="title"><?= esc($k['mk_nama']) ?> <span class="muted">· Kelas <?= esc($k['nama_kelas']) ?></span></span>
          <span class="sub"><?= esc($k['kode']) ?> · <?= nama_hari((int) $k['hari']) ?> <?= jam($k['jam_mulai']) ?>–<?= jam($k['jam_selesai']) ?> · <?= esc($k['ruang']) ?></span></span>
        <span class="value num"><?= $k['jumlah_peserta'] ?></span><?= icon('chevron-right', 'chev') ?>
      </a>
    <?php endforeach ?>
    <?php if (! $kelas): ?><div class="row empty">Belum ada kelas di semester aktif.</div><?php endif ?>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Mahasiswa Perwalian</h2><a href="<?= site_url('dosen/perwalian') ?>">Detail</a></div>
  <div class="group has-avatars">
    <?php foreach (array_slice($wali, 0, 6) as $m): ?>
      <a class="row" href="<?= site_url('dosen/perwalian/' . $m['id']) ?>">
        <span class="avatar sm"><?= esc(inisial($m['nama'])) ?></span>
        <span class="grow"><span class="title"><?= esc($m['nama']) ?></span><span class="sub"><?= esc($m['nim']) ?></span></span>
        <span class="value num">IPK <?= format_ip($m['ipk']) ?></span><?= icon('chevron-right', 'chev') ?>
      </a>
    <?php endforeach ?>
    <?php if (! $wali): ?><div class="row empty">Belum ada mahasiswa perwalian.</div><?php endif ?>
  </div>
</section>
<?= $this->endSection() ?>
