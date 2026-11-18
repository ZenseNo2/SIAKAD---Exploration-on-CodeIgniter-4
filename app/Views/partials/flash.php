<?php if ($msg = session()->getFlashdata('success')): ?>
<div class="alert success" role="status"><?= icon('check') ?><div class="grow"><?= esc($msg) ?></div><button type="button" class="close" aria-label="Tutup"><?= icon('x') ?></button></div>
<?php endif ?>
<?php if ($msg = session()->getFlashdata('error')): ?>
<div class="alert error" role="alert"><?= icon('alert') ?><div class="grow"><?= esc($msg) ?></div><button type="button" class="close" aria-label="Tutup"><?= icon('x') ?></button></div>
<?php endif ?>
<?php $errors = session('_ci_validation_errors'); if (is_array($errors) && count($errors) > 1): ?>
<div class="alert error" role="alert"><?= icon('alert') ?><div class="grow">Periksa kembali <?= count($errors) ?> isian yang ditandai merah.</div></div>
<?php endif ?>
