<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/dosen/new') ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form class="toolbar" method="get" action="<?= site_url('admin/dosen') ?>">
  <label class="search"><?= icon('search') ?><span class="sr-only">Cari</span>
    <input type="search" name="q" value="<?= esc($cari) ?>" placeholder="Cari nama atau NIDN">
  </label>
</form>

<section class="section">
  <div class="section-head"><h2>Daftar Dosen</h2><span class="meta"><?= $pager->getTotal() ?> orang</span></div>
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>NIDN</th><th>Nama</th><th class="hide-sm">Program Studi</th><th class="r">Kelas Semester Ini</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="nowrap"><?= esc($r['nidn']) ?></td>
          <td><?= esc($r['nama']) ?><span class="sub"><?= esc($r['email'] ?? '') ?></span></td>
          <td class="hide-sm"><?= esc($r['prodi_nama'] ?? '–') ?></td>
          <td class="r"><?= (int) $r['jumlah_kelas'] ?></td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url("admin/dosen/{$r['id']}/edit") ?>">Ubah</a>
            <form action="<?= site_url("admin/dosen/{$r['id']}/delete") ?>" method="post"
                  data-confirm="Hapus <?= esc($r['nama'], 'attr') ?>?" data-confirm-text="Dosen yang masih mengajar kelas tidak dapat dihapus.">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $rows): ?><tr><td colspan="5" class="c muted" style="padding:22px">Tidak ada dosen yang cocok.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
  <?= $pager->links('default', 'ios') ?>
</section>
<?= $this->endSection() ?>
