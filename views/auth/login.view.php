<?php

use App\core\Application;

/** @var $this \app\core\Renderer  */
$this->title .= ' - Log in';
?>

<div class="max-w-[350px] mx-auto mt-10">
    <section class="p-6 border border-gray-100 shadow-md shadow-gray-200 rounded-lg">
        <h2 class="text-center text-3xl font-medium mb-2.5">Log in</h2>
        <p class="text-center text-xs text-gray-400 mb-6">Not registered yet?
            <span class="text-indigo-500"><a href="/register">Sign in</a></span>
        </p>

        <form action="login" method="POST">
            <div class="mb-5">
                <label for="email" class="block mb-1 text-gray-600 text-xs">
                    Email
                </label>
                <input name="email" placeholder="Example@email.com" type="email" value="<?= Application::$app->session->getOldData('email') ?>" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">

                <p class="text-red-500 font-medium text-xs mt-1.5">
                    <?= Application::$app->session->getValidationErrors('email') ?>
                </p>
            </div>

            <div class="mb-2">
                <label for="password" class="block mb-1 text-gray-600 text-xs">
                    Password
                </label>
                <input name="password" placeholder="Enter your password" type="password" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">

                <p class="text-red-500 font-medium text-xs mt-1.5">
                    <?= Application::$app->session->getValidationErrors('password') ?>
                </p>
            </div>
            <a class="block text-xs text-gray-400 hover:underline mb-6" href="/forgot-password">Forgot password?</a>

            <button type="submit" class="tracking-wide w-full bg-indigo-400 hover:bg-indigo-500 text-white p-3 rounded-md text-xs">
                Log in
            </button>
        </form>
    </section>
</div>