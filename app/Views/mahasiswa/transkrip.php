<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<button class="bar-btn" type="button" onclick="window.print()"><?= icon('printer') ?><span class="hide-sm">Cetak</span></button>
<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= esc($mhs['nama']) ?> · <?= esc($mhs['nim']) ?> · <?= esc($mhs['jenjang'] . ' ' . $mhs['prodi_nama']) ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="print-only" style="margin-bottom:10px"><b>TRANSKRIP NILAI SEMENTARA</b></div>

<section class="section">
  <div class="group">
    <dl class="kv">
      <div><dt>IPK</dt><dd><?= format_ip($ipk['ip']) ?></dd></div>
      <div><dt>SKS dinilai</dt><dd><?= $ipk['sks'] ?></dd></div>
      <div><dt>SKS lulus</dt><dd><?= $ipk['sks_lulus'] ?></dd></div>
      <div><dt>Total mutu</dt><dd><?= number_format($ipk['mutu'], 1, ',', '') ?></dd></div>
    </dl>
  </div>
</section>

<?php foreach ($grup as $smt => $rows): ?>
<section class="section">
  <div class="section-head"><h2>Semester <?= $smt ?></h2><span class="meta"><?= array_sum(array_column($rows, 'sks')) ?> SKS</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th style="width:90px">Kode</th><th>Mata Kuliah</th><th class="c" style="width:60px">SKS</th><th class="c" style="width:70px">Huruf</th><th class="r" style="width:70px">Mutu</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= esc($r['kode']) ?></td>
          <td><?= esc($r['mk_nama']) ?></td>
          <td class="c"><?= $r['sks'] ?></td>
          <td class="c"><span class="<?= kelas_huruf($r['nilai_huruf']) ?>"><?= esc($r['nilai_huruf']) ?></span></td>
          <td class="r"><?= number_format($r['sks'] * bobot_huruf($r['nilai_huruf']), 1, ',', '') ?></td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</section>
<?php endforeach ?>

<?php if (! $grup): ?>
  <section class="section"><div class="group"><div class="row empty">Belum ada mata kuliah yang dinilai.</div></div></section>
<?php else: ?>
  <p class="section-foot" style="margin-top:12px">Jika satu mata kuliah diambil lebih dari sekali, yang tercantum adalah nilai terbaiknya.</p>
<?php endif ?>
<?= $this->endSection() ?>
