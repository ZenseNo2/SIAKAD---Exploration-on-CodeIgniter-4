<?php

use App\Models\TahunAkademikModel;

?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= tanggal_id() ?> · <?= esc($mhs['nim']) ?> · <?= esc($mhs['jenjang'] . ' ' . $mhs['prodi_nama']) ?> · Angkatan <?= $mhs['angkatan'] ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stats">
  <div class="stat"><div class="k"><?= icon('cap') ?>IPK</div><div class="v"><?= format_ip($ipk['ip']) ?></div><div class="d">dari 4,00</div></div>
  <div class="stat"><div class="k"><?= icon('check') ?>SKS Lulus</div><div class="v"><?= $ipk['sks_lulus'] ?></div><div class="d">kumulatif</div></div>
  <div class="stat"><div class="k"><?= icon('chart') ?>IPS Terakhir</div><div class="v"><?= format_ip($terakhir['ips'] ?? null) ?></div><div class="d"><?= esc($terakhir['label'] ?? 'belum ada') ?></div></div>
  <div class="stat"><div class="k"><?= icon('checklist') ?>SKS Semester Ini</div><div class="v"><?= $sksKrs ?><small> / <?= $batas['batas'] ?? '–' ?></small></div><div class="d">diambil / batas</div></div>
</div>

<?php if ($ta && $ta['krs_dibuka'] && $sksKrs < ($batas['batas'] ?? 0)): ?>
  <div class="alert info"><?= icon('info') ?><div class="grow">Periode KRS <?= esc(TahunAkademikModel::label($ta)) ?> sedang dibuka. Anda masih bisa mengambil <?= $batas['batas'] - $sksKrs ?> SKS lagi.</div><a class="btn small" href="<?= site_url('mahasiswa/krs') ?>">Isi KRS</a></div>
<?php endif ?>

<section class="section">
  <div class="section-head"><h2>Kuliah Hari Ini</h2><a href="<?= site_url('mahasiswa/jadwal') ?>">Minggu ini</a></div>
  <div class="group">
    <?php foreach ($jadwal as $k): ?>
      <div class="row">
        <div class="slot grow">
          <div class="time"><b><?= jam($k['jam_mulai']) ?></b><?= jam($k['jam_selesai']) ?></div>
          <div class="info"><span class="title"><?= esc($k['mk_nama']) ?></span><span class="sub"><?= esc($k['ruang']) ?> · <?= esc($k['dosen_nama']) ?></span></div>
        </div>
      </div>
    <?php endforeach ?>
    <?php if (! $jadwal): ?><div class="row empty">Tidak ada kuliah hari ini.</div><?php endif ?>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Perkembangan IPS</h2><a href="<?= site_url('mahasiswa/khs') ?>">Lihat KHS</a></div>
  <div class="group">
    <?php $adaNilai = false; foreach ($semester as $s): if ($s['ips'] === null) continue; $adaNilai = true; ?>
      <div class="row">
        <span style="width:150px" class="nowrap"><?= esc($s['label']) ?></span>
        <span class="grow"><span class="meter"><span style="width:<?= $s['ips'] / 4 * 100 ?>%"></span></span></span>
        <span class="num" style="width:44px;text-align:right;font-weight:600"><?= format_ip($s['ips']) ?></span>
      </div>
    <?php endforeach ?>
    <?php if (! $adaNilai): ?><div class="row empty">Belum ada nilai.</div><?php endif ?>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>KRS <?= esc(TahunAkademikModel::label($ta)) ?></h2><span class="meta"><?= count($krs) ?> mata kuliah · <?= $sksKrs ?> SKS</span></div>
  <div class="group">
    <?php foreach ($krs as $k): ?>
      <div class="row">
        <span class="grow"><span class="title"><?= esc($k['mk_nama']) ?></span><span class="sub"><?= esc($k['kode']) ?> · Kelas <?= esc($k['nama_kelas']) ?> · <?= esc($k['dosen_nama']) ?></span></span>
        <span class="value"><?= $k['sks'] ?> SKS</span>
      </div>
    <?php endforeach ?>
    <?php if (! $krs): ?><div class="row empty">KRS semester ini masih kosong.</div><?php endif ?>
  </div>
</section>
<?= $this->endSection() ?>
