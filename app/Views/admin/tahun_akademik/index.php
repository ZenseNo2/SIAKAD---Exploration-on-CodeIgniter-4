<?= $this->extend('layouts/app') ?>

<?= $this->section('subtitle') ?>
<p class="subtitle">Hanya satu semester yang aktif. Mahasiswa dapat mengisi KRS saat periode KRS semester aktif dibuka.</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
  <div class="table-wrap">
    <table class="list">
      <thead><tr><th>Tahun Akademik</th><th>Status</th><th class="r hide-sm">Kelas</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><b><?= esc($r['tahun']) ?></b> <?= esc($r['semester']) ?></td>
          <td>
            <?php if ($r['is_active']): ?>
              <span class="badge blue">Aktif</span>
              <span class="badge <?= $r['krs_dibuka'] ? 'green' : '' ?>"><?= $r['krs_dibuka'] ? 'KRS dibuka' : 'KRS ditutup' ?></span>
            <?php else: ?>
              <span class="muted">–</span>
            <?php endif ?>
          </td>
          <td class="r hide-sm"><?= $r['jumlah_kelas'] ?></td>
          <td class="actions">
            <?php if ($r['is_active']): ?>
              <form action="<?= site_url("admin/tahun-akademik/{$r['id']}/krs") ?>" method="post">
                <?= csrf_field() ?><button class="btn small <?= $r['krs_dibuka'] ? 'gray' : 'tinted' ?>" type="submit"><?= $r['krs_dibuka'] ? 'Tutup KRS' : 'Buka KRS' ?></button>
              </form>
            <?php else: ?>
              <form action="<?= site_url("admin/tahun-akademik/{$r['id']}/aktifkan") ?>" method="post"
                    data-confirm="Aktifkan <?= esc($r['tahun'] . ' ' . $r['semester'], 'attr') ?>?" data-confirm-text="Semester aktif sebelumnya akan dinonaktifkan dan periode KRS-nya ditutup." data-confirm-ok="Aktifkan">
                <?= csrf_field() ?><button class="link-btn small" type="submit">Aktifkan</button>
              </form>
              <form action="<?= site_url("admin/tahun-akademik/{$r['id']}/delete") ?>" method="post" data-confirm="Hapus tahun akademik ini?" data-confirm-text="Hanya bisa dihapus jika belum memiliki kelas.">
                <?= csrf_field() ?><button class="link-btn small danger" type="submit">Hapus</button>
              </form>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</section>

<form method="post" action="<?= site_url('admin/tahun-akademik') ?>">
  <?= csrf_field() ?>
  <section class="section">
    <div class="section-head"><h2>Tambah Tahun Akademik</h2></div>
    <div class="group form-group">
      <?= field_row('tahun', 'Tahun', '<input id="tahun" name="tahun" value="' . esc(old('tahun')) . '" placeholder="mis. 2027/2028" pattern="\d{4}/\d{4}" required>') ?>
      <div class="row stack <?= has_error('semester') ?>">
        <div class="field">
          <label>Semester</label>
          <?php $smt = old('semester', 'Ganjil'); ?>
          <div class="segmented">
            <input type="radio" id="smt-ganjil" name="semester" value="Ganjil" <?= $smt === 'Ganjil' ? 'checked' : '' ?>><label for="smt-ganjil">Ganjil</label>
            <input type="radio" id="smt-genap" name="semester" value="Genap" <?= $smt === 'Genap' ? 'checked' : '' ?>><label for="smt-genap">Genap</label>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="form-actions"><button class="btn" type="submit">Tambah</button></div>
</form>
<?= $this->endSection() ?>
