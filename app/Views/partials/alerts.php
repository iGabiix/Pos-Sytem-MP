<?php if ($success = session()->getFlashdata('success')): ?><div class="alert success" role="status"><?= icon('check') ?><span><?= esc($success) ?></span></div><?php endif ?>
<?php if ($error = session()->getFlashdata('error')): ?><div class="alert error" role="alert"><?= icon('alert') ?><span><?= esc($error) ?></span></div><?php endif ?>
<?php if ($errors = session()->getFlashdata('errors')): ?><div class="alert error" role="alert"><?= icon('alert') ?><div><strong>Please check the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul></div></div><?php endif ?>

