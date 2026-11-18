<?php

use App\Models\TahunAkademikModel;

?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle">Mahasiswa bimbingan akademik Anda. SKS KRS dihitung untuk semester <?= esc(TahunAkademikModel::label($ta)) ?>.</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Mahasiswa</th><th class="hide-sm">Prodi</th><th class="r">IPK</th><th class="r">SKS Lulus</th><th class="r">SKS KRS</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $m): ?>
        <tr>
          <td><?= esc($m['nama']) ?><span class="sub"><?= esc($m['nim']) ?> · Angkatan <?= $m['angkatan'] ?></span></td>
          <td class="hide-sm"><?= esc($m['prodi_nama']) ?></td>
          <td class="r"><b><?= format_ip($m['ipk']) ?></b></td>
          <td class="r"><?= $m['sks_lulus'] ?></td>
          <td class="r"><?= $m['sks_krs'] ?: '<span class="badge orange">Belum KRS</span>' ?></td>
          <td class="actions"><a class="link-btn small" href="<?= site_url('dosen/perwalian/' . $m['id']) ?>">Riwayat</a></td>
        </tr>
      <?php endforeach ?>
      <?php if (! $rows): ?><tr><td colspan="6" class="c muted" style="padding:22px">Belum ada mahasiswa perwalian.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</section>
<?= $this->endSection() ?>
