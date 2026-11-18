<?= $this->extend('layouts/app') ?>

<?= $this->section('actions') ?>
<a class="bar-btn" href="<?= site_url('admin/prodi/new') ?>"><?= icon('plus') ?><span class="hide-sm">Tambah</span></a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Kode</th><th>Program Studi</th><th class="r">Mahasiswa</th><th class="r">Mata Kuliah</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= esc($r['kode']) ?></td>
          <td><?= esc($r['jenjang']) ?> <?= esc($r['nama']) ?></td>
          <td class="r"><?= $r['jumlah_mahasiswa'] ?></td>
          <td class="r"><?= $r['jumlah_mk'] ?></td>
          <td class="actions">
            <a class="link-btn small" href="<?= site_url("admin/prodi/{$r['id']}/edit") ?>">Ubah</a>
            <form action="<?= site_url("admin/prodi/{$r['id']}/delete") ?>" method="post" data-confirm="Hapus <?= esc($r['nama'], 'attr') ?>?" data-confirm-text="Hanya bisa dihapus jika belum memiliki mahasiswa dan mata kuliah.">
              <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
      <?php if (! $rows): ?><tr><td colspan="5" class="c muted" style="padding:22px">Belum ada program studi.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</section>
<?= $this->endSection() ?>
