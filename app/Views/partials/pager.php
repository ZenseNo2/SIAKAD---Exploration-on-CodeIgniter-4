<?php
/**
 * Template pagination (didaftarkan di Config/Pager.php sebagai 'ios').
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
if ($pager->getPageCount() <= 1) {
    return;
}
?>
<nav class="pager" aria-label="Halaman">
  <?php if ($pager->hasPreviousPage()): ?>
    <a href="<?= $pager->getPreviousPage() ?>" aria-label="Sebelumnya"><?= icon('chevron-left') ?></a>
  <?php else: ?>
    <span class="off"><?= icon('chevron-left') ?></span>
  <?php endif ?>

  <?php foreach ($pager->links() as $link): ?>
    <?php if ($link['active']): ?>
      <span class="on" aria-current="page"><?= $link['title'] ?></span>
    <?php else: ?>
      <a href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
    <?php endif ?>
  <?php endforeach ?>

  <?php if ($pager->hasNextPage()): ?>
    <a href="<?= $pager->getNextPage() ?>" aria-label="Berikutnya"><?= icon('chevron-right') ?></a>
  <?php else: ?>
    <span class="off"><?= icon('chevron-right') ?></span>
  <?php endif ?>
</nav>
