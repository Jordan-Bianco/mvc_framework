<?php

use App\core\Application;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= Application::$app->renderer->title ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="text-sm text-slate-700 tracking-wide">

    <?php require_once(ROOT_PATH . '/views/layouts/navbar.php') ?>

    <div class="p-6 max-w-6xl mx-auto">

        <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-500 p-4 rounded-lg mt-2 mb-6">
            <?php Application::$app->session->getFlashMessage('success') ?>
        </div>
        <?php endif ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-500 p-4 rounded-lg mt-2 mb-6">
            <?php Application::$app->session->getFlashMessage('error') ?>
        </div>
        <?php endif ?>

        <?php if (isset($_SESSION['info'])): ?>
        <div class="bg-blue-100 text-blue-500 p-4 rounded-lg mt-2 mb-6">
            <?php Application::$app->session->getFlashMessage('info') ?>
        </div>
        <?php endif ?>

        {{ content }}
    </div>
</body>

</html>