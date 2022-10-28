<?php

use App\core\Application;

/** @var $this \app\core\Renderer  */
$this->title .= ' - Password dimenticata';
?>

<div class="max-w-[350px] mx-auto mt-10">
    <section class="p-6 border border-gray-100 shadow-md shadow-gray-200 rounded-lg">
        <h2 class="text-center text-3xl font-medium mb-2.5">Recupera password</h2>
        <p class="text-center text-gray-400 text-xs mb-6">Inserisci il tuo indirizzo email, ti verrà inviata una mail contenente un link per resettare la password.</p>

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

        <form action="/forgot-password" method="POST">
            <div class="mb-5">
                <label for="email" class="block mb-1 text-gray-600 text-xs">
                    Email
                </label>
                <input name="email" placeholder="Inserisci la tua email" type="email" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="tracking-wide w-full bg-indigo-400 hover:bg-indigo-500 text-white p-3 rounded-md text-xs">
                Invia email
            </button>
        </form>
    </section>
</div>