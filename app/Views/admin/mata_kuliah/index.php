<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/mata-kuliah/new') ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form class="toolbar" method="get" action="<?= site_url('admin/mata-kuliah') ?>">
  <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
    <input type="search" name="q" value="<?= esc($cari) ?>" placeholder="Cari kode atau nama">
  </label>
  <select class="select-pill" name="prodi" data-autosubmit aria-label="Program studi">
    <?= select_options($prodi, $prodiId, 'Semua prodi') ?>
  </select>
</form>

<section class="section">
  <div class="section-head"><h2>Kurikulum</h2><span class="meta"><?= $pager->getTotal() ?> mata kuliah</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Kode</th><th>Mata Kuliah</th><th class="c">SKS</th><th class="c">Smt</th><th class="hide-sm">Prodi</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= esc($r['kode']) ?></td>
          <td><?= esc($r['nama']) ?></td>
          <td class="c"><?= $r['sks'] ?></td>
          <td class="c"><?= $r['semester'] ?></td>
          <td class="hide-sm"><?= esc($r['prodi_nama']) ?></td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url("admin/mata-kuliah/{$r['id']}/edit") ?>">Ubah</a>
            <form action="<?= site_url("admin/mata-kuliah/{$r['id']}/delete") ?>" method="post" data-confirm="Hapus <?= esc($r['nama'], 'attr') ?>?" data-confirm-text="Mata kuliah yang sudah dipakai di jadwal tidak dapat dihapus.">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $rows): ?><tr><td colspan="6" class="c muted" style="padding:22px">Tidak ada mata kuliah yang cocok.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
</section>
<?= $this->endSection() ?>
