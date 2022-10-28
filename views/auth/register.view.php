<?php

use App\core\Application;
use App\core\form\Form;

/** @var $this \app\core\Renderer  */
$this->title .= ' - Registrati';
?>

<div class="max-w-[350px] mx-auto mt-10">
    <section class="p-6 border border-gray-100 shadow-md shadow-gray-200 rounded-lg">
        <h2 class="text-center text-3xl font-medium mb-2.5">Sign up</h2>
        <p class="text-center text-xs text-gray-400 mb-6">Già iscritto?
            <span class="text-indigo-500"><a href="/login">Accedi</a></span>
        </p>

        <!-- ValidationErrors -->
        <?php if (isset($_SESSION['validationErrors'])) : ?>
            <div class="bg-red-100 text-red-500 font-medium p-4 rounded-lg my-3 text-xs">
                <?php foreach (Application::$app->session->getValidationErrors() as $error) : ?>
                    <span class="block">
                        <?= $error ?>
                    </span>
                <?php endforeach ?>
                <?php Application::$app->session->removeValidationErrors() ?>
            </div>
        <?php endif ?>

        <form action="register" method="POST">
            <div class="mb-5">
                <label for="username" class="block mb-1 text-gray-600 text-xs">
                    Username
                </label>
                <input name="username" placeholder="Username" type="text" value="<?= isset($data['username']) ? $data['username'] : '' ?>" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-5">
                <label for="email" class="block mb-1 text-gray-600 text-xs">
                    Email
                </label>
                <input name="email" placeholder="Example@email.com" type="email" value="<?= isset($data['email']) ? $data['email'] : '' ?>" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-5">
                <label for="password" class="block mb-1 text-gray-600 text-xs">
                    Password
                </label>
                <input name="password" placeholder="Inserisci la tua password" type="password" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-6">
                <label for="password_confirm" class="block mb-1 text-gray-600 text-xs">
                    Conferma Password
                </label>
                <input name="password_confirm" placeholder="Conferma la tua password" type="password" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="tracking-wide w-full bg-indigo-400 hover:bg-indigo-500 text-white p-3 rounded-md text-xs">
                Registrati
            </button>
        </form>
    </section>
</div>