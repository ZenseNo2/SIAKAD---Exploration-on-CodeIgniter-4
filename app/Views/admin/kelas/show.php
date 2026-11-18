<?= $this->extend('layouts/app') ?>

<?= $this->section('back') ?>
<a class="back-link" href="<?= site_url('admin/kelas?ta=' . $kelas['tahun_akademik_id']) ?>"><?= icon('chevron-left') ?>Jadwal</a>
<?= $this->endSection() ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url("admin/kelas/{$kelas['id']}/edit") ?>">Ubah</a>
<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
<p class="subtitle">Kelas <?= esc($kelas['nama_kelas']) ?> · <?= esc($kelas['tahun'] . ' ' . $kelas['ta_semester']) ?></p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="group">
    <dl class="kv">
      <div><dt>Dosen</dt><dd style="font-size:15px"><?= esc($kelas['dosen_nama']) ?></dd></div>
      <div><dt>Jadwal</dt><dd style="font-size:15px"><?= nama_hari((int) $kelas['hari']) ?>, <?= jam($kelas['jam_mulai']) ?>–<?= jam($kelas['jam_selesai']) ?></dd></div>
      <div><dt>Ruang</dt><dd style="font-size:15px"><?= esc($kelas['ruang']) ?></dd></div>
      <div><dt>Peserta</dt><dd><?= $kelas['jumlah_peserta'] ?> <span class="muted" style="font-weight:400">/ <?= $kelas['kuota'] ?></span></dd></div>
    </dl>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>Peserta</h2></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th style="width:40px">#</th><th>NIM</th><th>Nama</th><th class="r">Nilai Akhir</th><th class="c">Huruf</th></tr></thead>
      <tbody>
      <?php foreach ($peserta as $i => $p): ?>
        <tr>
          <td class="muted"><?= $i + 1 ?></td>
          <td><?= esc($p['nim']) ?></td>
          <td><?= esc($p['nama']) ?></td>
          <td class="r"><?= $p['nilai_akhir'] !== null ? number_format((float) $p['nilai_akhir'], 2, ',', '') : '–' ?></td>
          <td class="c"><span class="<?= kelas_huruf($p['nilai_huruf']) ?>"><?= esc($p['nilai_huruf'] ?? '–') ?></span></td>
        </tr>
      <?php endforeach ?>
      <?php if (! $peserta): ?><tr><td colspan="5" class="c muted" style="padding:22px">Belum ada peserta.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</section>
<?= $this->endSection() ?>
