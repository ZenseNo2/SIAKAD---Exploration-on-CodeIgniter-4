<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('dosen/perwalian') ?>"><?= icon('chevron-left') ?>Perwalian</a>
<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= esc($mhs['nim']) ?> · <?= esc($mhs['prodi_nama']) ?> · Angkatan <?= $mhs['angkatan'] ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="group">
    <dl class="kv">
      <div><dt>IPK</dt><dd><?= format_ip($ipk['ip']) ?></dd></div>
      <div><dt>SKS Lulus</dt><dd><?= $ipk['sks_lulus'] ?></dd></div>
      <div><dt>Semester ditempuh</dt><dd><?= count($semester) ?></dd></div>
      <div><dt>Email</dt><dd style="font-size:14px;font-weight:400"><?= esc($mhs['email'] ?? '–') ?></dd></div>
    </dl>
  </div>
</section>

<?php foreach ($semester as $s): ?>
<section class="section">
  <div class="section-head"><h2><?= esc($s['label']) ?></h2><span class="meta">IPS <?= format_ip($s['ips']) ?> · IPK <?= format_ip($s['ipk']) ?></span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Kode</th><th>Mata Kuliah</th><th class="c">SKS</th><th class="r">Nilai</th><th class="c">Huruf</th></tr></thead>
      <tbody>
      <?php foreach ($s['rows'] as $r): ?>
        <tr>
          <td><?= esc($r['kode']) ?></td>
          <td><?= esc($r['mk_nama']) ?></td>
          <td class="c"><?= $r['sks'] ?></td>
          <td class="r"><?= $r['nilai_akhir'] !== null ? number_format((float) $r['nilai_akhir'], 2, ',', '') : '–' ?></td>
          <td class="c"><span class="<?= kelas_huruf($r['nilai_huruf']) ?>"><?= esc($r['nilai_huruf'] ?? '–') ?></span></td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</section>
<?php endforeach ?>
<?= $this->endSection() ?>
