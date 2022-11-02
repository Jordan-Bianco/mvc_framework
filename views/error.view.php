<?php

/** @var $this \app\core\Renderer  */
$this->title .= ' - Error';
?>

<div class="bg-indigo-100 border-indigo-300 text-indigo-500 p-4 rounded">
    Oops .. Something went wrong!

    <span class="font-semibold">
        <?= $error->getCode() . ' - ' . $error->getMessage() ?>
    </span>
</div>