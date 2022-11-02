<?php

use App\core\Application;

/** @var $this \app\core\Renderer  */
$this->title .= ' - Forgot password';
?>

<div class="max-w-[350px] mx-auto mt-10">
    <section class="p-6 border border-gray-100 shadow-md shadow-gray-200 rounded-lg">
        <h2 class="text-center text-3xl font-medium mb-2.5">Recovery password</h2>
        <p class="text-center text-gray-400 text-xs mb-6">Enter your email address, an email will be sent to you containing a link to reset your password.</p>

        <!-- ValidationErrors -->
        <?php require_once ROOT_PATH . '/views/partials/validationErrors.php' ?>

        <form action="/forgot-password" method="POST">
            <div class="mb-5">
                <label for="email" class="block mb-1 text-gray-600 text-xs">
                    Email
                </label>
                <input name="email" placeholder="Enter your email" type="email" value="<?= Application::$app->session->getOldData('email') ?>" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="tracking-wide w-full bg-indigo-400 hover:bg-indigo-500 text-white p-3 rounded-md text-xs">
                Send email
            </button>
        </form>
    </section>
</div>