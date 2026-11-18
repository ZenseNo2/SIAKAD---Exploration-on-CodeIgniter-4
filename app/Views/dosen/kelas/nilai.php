<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('dosen/kelas') ?>"><?= icon('chevron-left') ?>Kelas</a>
<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
<p class="subtitle">Kelas <?= esc($kelas['nama_kelas']) ?> · <?= esc($kelas['tahun'] . ' ' . $kelas['ta_semester']) ?> · <?= nama_hari((int) $kelas['hari']) ?> <?= jam($kelas['jam_mulai']) ?>–<?= jam($kelas['jam_selesai']) ?> · <?= esc($kelas['ruang']) ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$dinilai = count(array_filter($peserta, static fn ($p) => $p['nilai_huruf'] !== null));
$skalaJs = array_map(static fn ($s) => ['min' => $s['min'], 'huruf' => $s['huruf']], $skala);
?>
<section class="section">
  <div class="group">
    <dl class="kv">
      <div><dt>Peserta</dt><dd><?= count($peserta) ?></dd></div>
      <div><dt>Sudah dinilai</dt><dd><?= $dinilai ?></dd></div>
      <div><dt>Bobot</dt><dd style="font-size:15px">Tugas <?= $bobot['tugas'] ?>% · UTS <?= $bobot['uts'] ?>% · UAS <?= $bobot['uas'] ?>%</dd></div>
      <div><dt>Skala</dt><dd style="font-size:13px;font-weight:400;line-height:1.5"><?= implode(' · ', array_map(static fn ($s) => '<span class="nowrap">' . $s['huruf'] . ' ≥' . $s['min'] . '</span>', array_slice($skala, 0, -1))) ?></dd></div>
    </dl>
  </div>
</section>

<form method="post" action="<?= site_url('dosen/kelas/' . $kelas['id']) ?>">
  <?= csrf_field() ?>
  <section class="section">
    <div class="section-head"><h2>Nilai Peserta</h2><span class="meta">Kosongkan bila belum ada nilai</span></div>
    <div class="table-wrap">
      <table class="list" data-grade-sheet data-bobot='<?= esc(json_encode($bobot), 'attr') ?>' data-skala='<?= esc(json_encode($skalaJs), 'attr') ?>'>
        <thead><tr><th>Mahasiswa</th><th class="c">Tugas</th><th class="c">UTS</th><th class="c">UAS</th><th class="r">Akhir</th><th class="c">Huruf</th></tr></thead>
        <tbody>
        <?php foreach ($peserta as $p): $f = static fn ($v) => $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); ?>
          <tr data-row>
            <td><?= esc($p['nama']) ?><span class="sub"><?= esc($p['nim']) ?></span></td>
            <?php foreach (['tugas', 'uts', 'uas'] as $k): ?>
              <td class="c"><input class="score-input" data-k="<?= $k ?>" name="nilai[<?= $p['id'] ?>][<?= $k ?>]" value="<?= $f($p['nilai_' . $k]) ?>" inputmode="decimal" autocomplete="off" aria-label="<?= strtoupper($k) ?> <?= esc($p['nama'], 'attr') ?>"></td>
            <?php endforeach ?>
            <td class="r" data-akhir>–</td>
            <td class="c"><span class="grade" data-huruf>–</span></td>
          </tr>
        <?php endforeach ?>
        <?php if (! $peserta): ?><tr><td colspan="6" class="c muted" style="padding:22px">Belum ada mahasiswa yang mengambil kelas ini.</td></tr><?php endif ?>
        </tbody>
      </table>
    </div>
  </section>
  <?php if ($peserta): ?>
    <div class="form-actions"><button class="btn" type="submit">Simpan Nilai</button></div>
  <?php endif ?>
</form>
<?= $this->endSection() ?>
