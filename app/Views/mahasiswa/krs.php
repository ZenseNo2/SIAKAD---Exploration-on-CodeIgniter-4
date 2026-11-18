<?php

use App\Models\TahunAkademikModel;

?>
<?= $this->extend('layouts/app') ?>

<?php if ($ta): ?>
<?= $this->section('actions') ?>
<button class="bar-btn" type="button" onclick="window.print()"><?= icon('printer') ?><span class="hide-sm">Cetak</span></button>
<?= $this->endSection() ?>

<?= $this->section('subtitle') ?>
<p class="subtitle"><?= esc(TahunAkademikModel::label($ta)) ?> ·
  <span class="badge <?= $ta['krs_dibuka'] ? 'green' : '' ?>"><?= $ta['krs_dibuka'] ? 'Periode KRS dibuka' : 'Periode KRS ditutup' ?></span>
</p>
<?= $this->endSection() ?>
<?php endif ?>

<?= $this->section('content') ?>
<?php if (! $ta): ?>
  <section class="section"><div class="group"><div class="row empty">Belum ada semester aktif. Hubungi bagian akademik.</div></div></section>
<?php else:
    $persen = $batas['batas'] > 0 ? min(100, round($totalSks / $batas['batas'] * 100)) : 0; ?>

<div class="print-only" style="margin-bottom:10px">
  <b>KARTU RENCANA STUDI</b><br><?= esc($mhs['nama']) ?> · <?= esc($mhs['nim']) ?> · <?= esc(TahunAkademikModel::label($ta)) ?>
</div>

<section class="section">
  <div class="group">
    <dl class="kv">
      <div><dt>SKS diambil</dt><dd><?= $totalSks ?></dd></div>
      <div><dt>Batas SKS</dt><dd><?= $batas['batas'] ?></dd></div>
      <div><dt>Sisa</dt><dd><?= max(0, $batas['batas'] - $totalSks) ?></dd></div>
      <div><dt>Dasar batas</dt><dd style="font-size:14px;font-weight:400"><?= esc($batas['dasar']) ?><?= $batas['ips'] !== null ? ' = ' . format_ip($batas['ips']) : '' ?></dd></div>
    </dl>
    <div style="padding:0 16px 14px"><div class="meter <?= $persen >= 100 ? 'full' : '' ?>"><span style="width:<?= $persen ?>%"></span></div></div>
  </div>
</section>

<section class="section">
  <div class="section-head"><h2>KRS Anda</h2><span class="meta"><?= count($diambil) ?> mata kuliah</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Mata Kuliah</th><th class="c">SKS</th><th>Jadwal</th><th class="hide-sm">Dosen</th><th class="no-print"></th></tr></thead>
      <tbody>
      <?php foreach ($diambil as $k): ?>
        <tr>
          <td><?= esc($k['mk_nama']) ?><span class="sub"><?= esc($k['kode']) ?> · Kelas <?= esc($k['nama_kelas']) ?></span></td>
          <td class="c"><?= $k['sks'] ?></td>
          <td class="nowrap"><?= nama_hari((int) $k['hari']) ?><span class="sub"><?= jam($k['jam_mulai']) ?>–<?= jam($k['jam_selesai']) ?> · <?= esc($k['ruang']) ?></span></td>
          <td class="hide-sm"><?= esc($k['dosen_nama']) ?></td>
          <td class="actions no-print">
            <?php if ($ta['krs_dibuka'] && $k['nilai_huruf'] === null): ?>
              <form action="<?= site_url("mahasiswa/krs/{$k['id']}/batal") ?>" method="post"
                    data-confirm="Batalkan <?= esc($k['mk_nama'], 'attr') ?>?" data-confirm-text="Mata kuliah ini akan dihapus dari KRS Anda." data-confirm-ok="Batalkan">
                <?= csrf_field() ?><button class="link-btn small danger" type="submit">Batalkan</button>
              </form>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $diambil): ?><tr><td colspan="5" class="c muted" style="padding:22px">Belum ada mata kuliah. Pilih dari daftar kelas di bawah.</td></tr><?php endif ?>
      </tbody>
      <?php if ($diambil): ?>
        <tfoot><tr><td>Total</td><td class="c"><?= $totalSks ?></td><td colspan="3"></td></tr></tfoot>
      <?php endif ?>
    </table>
  </div>
</section>

<section class="section no-print">
  <div class="section-head"><h2>Kelas Ditawarkan</h2><span class="meta">Prodi Anda · <?= count($tersedia) ?> kelas</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Mata Kuliah</th><th class="c">SKS</th><th>Jadwal</th><th class="hide-sm">Dosen</th><th class="r">Kuota</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($tersedia as $k): ?>
        <tr>
          <td>
            <?= esc($k['mk_nama']) ?>
            <span class="sub"><?= esc($k['kode']) ?> · Kelas <?= esc($k['nama_kelas']) ?> · Smt <?= $k['mk_semester'] ?></span>
            <?php if ($k['alasan']): ?><span class="sub" style="color:var(--orange)"><?= esc($k['alasan']) ?></span><?php endif ?>
          </td>
          <td class="c"><?= $k['sks'] ?></td>
          <td class="nowrap"><?= nama_hari((int) $k['hari']) ?><span class="sub"><?= jam($k['jam_mulai']) ?>–<?= jam($k['jam_selesai']) ?> · <?= esc($k['ruang']) ?></span></td>
          <td class="hide-sm"><?= esc($k['dosen_nama']) ?></td>
          <td class="r nowrap"><?= $k['jumlah_peserta'] ?>/<?= $k['kuota'] ?></td>
          <td class="actions">
            <form action="<?= site_url('mahasiswa/krs/ambil') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="kelas_id" value="<?= $k['id'] ?>">
              <button class="btn small tinted" type="submit" <?= $k['alasan'] ? 'disabled' : '' ?>>Ambil</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $tersedia): ?><tr><td colspan="6" class="c muted" style="padding:22px">Tidak ada kelas lain yang ditawarkan.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <p class="section-foot">Kelas tidak bisa diambil bila jadwalnya bentrok, kuotanya penuh, mata kuliahnya sudah ada di KRS, atau total SKS melebihi batas.</p>
</section>
<?php endif ?>
<?= $this->endSection() ?>
