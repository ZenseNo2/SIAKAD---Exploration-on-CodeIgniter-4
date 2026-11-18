<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php foreach ($grup as $label => $list): ?>
<section class="section">
  <div class="section-head">
    <h2><?= esc($label) ?></h2>
    <?php if ($ta && $label === $ta['tahun'] . ' ' . $ta['semester']): ?><span class="badge blue">Semester aktif</span><?php endif ?>
  </div>
  <div class="group">
    <?php foreach ($list as $k): ?>
      <a class="row" href="<?= site_url('dosen/kelas/' . $k['id']) ?>">
        <span class="grow">
          <span class="title"><?= esc($k['mk_nama']) ?> <span class="muted">· Kelas <?= esc($k['nama_kelas']) ?></span></span>
          <span class="sub"><?= esc($k['kode']) ?> · <?= $k['sks'] ?> SKS · <?= nama_hari((int) $k['hari']) ?> <?= jam($k['jam_mulai']) ?>–<?= jam($k['jam_selesai']) ?> · <?= esc($k['ruang']) ?></span>
        </span>
        <span class="value"><?= $k['jumlah_peserta'] ?> peserta</span>
        <?= icon('chevron-right', 'chev') ?>
      </a>
    <?php endforeach ?>
  </div>
</section>
<?php endforeach ?>

<?php if (! $grup): ?>
  <section class="section"><div class="group"><div class="row empty">Anda belum memiliki kelas.</div></div></section>
<?php endif ?>
<?= $this->endSection() ?>
