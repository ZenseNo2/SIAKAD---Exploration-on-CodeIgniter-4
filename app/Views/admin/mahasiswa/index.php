<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/mahasiswa/new') ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form class="toolbar" method="get" action="<?= site_url('admin/mahasiswa') ?>">
  <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
    <input type="search" name="q" value="<?= esc($cari) ?>" placeholder="Cari nama atau NIM">
  </label>
  <select class="select-pill" name="prodi" data-autosubmit aria-label="Program studi">
    <?= select_options($prodi, $prodiId, 'Semua prodi') ?>
  </select>
</form>

<section class="section">
  <div class="section-head"><h2>Daftar Mahasiswa</h2><span class="meta"><?= $pager->getTotal() ?> orang</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>NIM</th><th>Nama</th><th class="hide-sm">Program Studi</th><th class="hide-sm">Angkatan</th><th class="hide-sm">Dosen Wali</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="nowrap"><?= esc($r['nim']) ?></td>
          <td><?= esc($r['nama']) ?><span class="sub"><?= $r['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?><?= $r['email'] ? ' · ' . esc($r['email']) : '' ?></span></td>
          <td class="hide-sm"><?= esc($r['prodi_nama']) ?></td>
          <td class="hide-sm"><?= $r['angkatan'] ?></td>
          <td class="hide-sm"><?= esc($r['wali_nama'] ?? '–') ?></td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url("admin/mahasiswa/{$r['id']}/edit") ?>">Ubah</a>
            <form action="<?= site_url("admin/mahasiswa/{$r['id']}/delete") ?>" method="post"
                  data-confirm="Hapus <?= esc($r['nama'], 'attr') ?>?" data-confirm-text="Akun login, KRS, dan seluruh nilainya juga akan dihapus.">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $rows): ?><tr><td colspan="6" class="c muted" style="padding:22px">Tidak ada mahasiswa yang cocok.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
</section>
<?= $this->endSection() ?>
