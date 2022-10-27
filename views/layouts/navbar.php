<?php

use App\core\Application;
use App\core\Session;
?>

<nav class="px-6 py-3 flex items-center justify-between border-b border-slate-200">
    <div class="flex items-center space-x-4 w-1/2">
        <h1 class="font-medium tracking-tight text-base">
            <a href="/">
                <?= $_ENV['SITE_NAME'] ?>
            </a>
        </h1>
        <span>|</span>
    </div>

    <div class="w-1/2 flex items-center justify-end space-x-2">
        <?php if (!Session::isLoggedIn()) : ?>
            <div class="flex items-center space-x-2">
                <a href="/login" class="block hover:text-indigo-500 <?= Application::$app->request->getUri() === 'login' ? 'text-indigo-500' : '' ?>">Login</a>
                <a href="/register" class="block hover:text-indigo-500 <?= Application::$app->request->getUri() === 'register' ? 'text-indigo-500' : '' ?>">Registrati</a>
            </div>
        <?php else : ?>
            <div class="flex items-center space-x-2">
                <span>
                    <?= Application::$app->session->get('user')['username'] ?>
                </span>
                <form action="/logout" method="POST">
                    <button type=" submit">Logout</button>
                </form>
            </div>
        <?php endif ?>
    </div>
</nav>