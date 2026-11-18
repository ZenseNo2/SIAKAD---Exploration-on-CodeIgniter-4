<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<button class="bar-btn" type="button" onclick="window.print()"><?= icon('printer') ?><span class="hide-sm">Cetak</span></button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (! $dipilih): ?>
  <section class="section"><div class="group"><div class="row empty">Belum ada riwayat studi.</div></div></section>
<?php else: ?>

<form class="toolbar no-print" method="get" action="<?= site_url('mahasiswa/khs') ?>">
  <select class="select-pill" name="ta" data-autosubmit aria-label="Semester">
    <?php foreach ($semester as $s): ?>
      <option value="<?= $s['ta_id'] ?>" <?= $s['ta_id'] === $dipilih['ta_id'] ? 'selected' : '' ?>><?= esc($s['label']) ?></option>
    <?php endforeach ?>
  </select>
</form>

<div class="print-only" style="margin-bottom:10px">
  <b>KARTU HASIL STUDI</b><br><?= esc($mhs['nama']) ?> · <?= esc($mhs['nim']) ?> · <?= esc($mhs['prodi_nama']) ?> · <?= esc($dipilih['label']) ?>
</div>

<section class="section">
  <div class="group">
    <dl class="kv">
      <div><dt>Semester</dt><dd style="font-size:15px"><?= esc($dipilih['label']) ?></dd></div>
      <div><dt>SKS dinilai</dt><dd><?= $dipilih['sks'] ?></dd></div>
      <div><dt>IPS</dt><dd><?= format_ip($dipilih['ips']) ?></dd></div>
      <div><dt>IPK s.d. semester ini</dt><dd><?= format_ip($dipilih['ipk']) ?></dd></div>
    </dl>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Nilai</h2></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Kode</th><th>Mata Kuliah</th><th class="c">SKS</th><th class="r hide-sm">Tugas</th><th class="r hide-sm">UTS</th><th class="r hide-sm">UAS</th><th class="r">Akhir</th><th class="c">Huruf</th><th class="r">Mutu</th></tr></thead>
      <tbody>
      <?php
      $f = static fn ($v) => $v === null ? '–' : number_format((float) $v, 2, ',', '');
      foreach ($dipilih['rows'] as $r):
          $bobot = bobot_huruf($r['nilai_huruf']); ?>
        <tr>
          <td><?= esc($r['kode']) ?></td>
          <td><?= esc($r['mk_nama']) ?><span class="sub"><?= esc($r['dosen_nama']) ?></span></td>
          <td class="c"><?= $r['sks'] ?></td>
          <td class="r hide-sm"><?= $f($r['nilai_tugas']) ?></td>
          <td class="r hide-sm"><?= $f($r['nilai_uts']) ?></td>
          <td class="r hide-sm"><?= $f($r['nilai_uas']) ?></td>
          <td class="r"><?= $f($r['nilai_akhir']) ?></td>
          <td class="c"><span class="<?= kelas_huruf($r['nilai_huruf']) ?>"><?= esc($r['nilai_huruf'] ?? '–') ?></span></td>
          <td class="r"><?= $bobot !== null ? number_format($r['sks'] * $bobot, 1, ',', '') : '–' ?></td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <p class="section-foot">Mutu = SKS × bobot huruf. IPS = jumlah mutu ÷ jumlah SKS yang sudah dinilai. Mata kuliah yang belum dinilai tidak dihitung.</p>
</section>
<?php endif ?>
<?= $this->endSection() ?>
