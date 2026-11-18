<?php

use App\Models\TahunAkademikModel;

?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle">Berdasarkan KRS <?= esc(TahunAkademikModel::label($ta)) ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="week" style="margin-top:8px">
  <?php foreach ($hari as $no => $h): ?>
    <section class="section day <?= $no === $hariIni ? 'today' : '' ?>">
      <div class="section-head"><h2><?= $h['nama'] ?><?= $no === $hariIni ? ' · Hari ini' : '' ?></h2></div>
      <div class="group">
        <?php foreach ($h['kelas'] as $k): ?>
          <div class="row">
            <div class="slot grow">
              <div class="time"><b><?= jam($k['jam_mulai']) ?></b><?= jam($k['jam_selesai']) ?></div>
              <div class="info"><span class="title"><?= esc($k['mk_nama']) ?></span><span class="sub"><?= esc($k['ruang']) ?> · Kelas <?= esc($k['nama_kelas']) ?></span><span class="sub"><?= esc($k['dosen_nama']) ?></span></div>
            </div>
          </div>
        <?php endforeach ?>
        <?php if (! $h['kelas']): ?><div class="row empty" style="padding:14px">Tidak ada kuliah</div><?php endif ?>
      </div>
    </section>
  <?php endforeach ?>
</div>
<?= $this->endSection() ?>
