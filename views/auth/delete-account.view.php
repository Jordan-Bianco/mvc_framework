<?php

/** @var $this \app\core\Renderer  */

use App\core\Application;

$this->title .= ' - Elimina account';
?>

<div class="max-w-[350px] mx-auto mt-10">
    <section class="p-6 border border-gray-100 shadow-md shadow-gray-200 rounded-lg">
        <h2 class="text-center text-3xl font-medium mb-2.5">Elimina account</h2>
        <p class="text-center text-gray-400 text-xs mb-6">Inserisci il tuo indirizzo email per cancellare il tuo account.</p>

        <!-- ValidationErrors -->
        <?php require_once ROOT_PATH . '/views/partials/validationErrors.php' ?>

        <form action="/delete-account" method="POST">
            <div class="mb-5">
                <label for="email" class="block mb-1 text-gray-600 text-xs">
                    Email
                </label>
                <input name="email" placeholder="Example@email.com" type="email" value="<?= Application::$app->session->getOldData('email') ?>" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="tracking-wide w-full bg-red-500 hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-300 text-white p-3 rounded-lg text-xs">
                Elimina account
            </button>
        </form>
    </section>
</div>