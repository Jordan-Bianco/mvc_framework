<?php

use App\core\Application;

/** @var $this \app\core\Renderer  */
$this->title .= ' - Password reset';


$queryString = $_SERVER['QUERY_STRING'] ?? false;

/** If the query string is not present, or the id and token parameters are not present, redirect home */
if (!$queryString || !isset($_GET['id']) || !isset($_GET['token'])) {
    Application::$app->response->redirect('/');
    return;
}

$params = explode('&', $queryString);

$id = substr($params[0], strpos($params[0], '=') + 1);
$token = substr($params[1], strpos($params[1], '=') + 1);

$user = Application::$app->builder
    ->select('users')
    ->where('id', $id)
    ->first();

/** If the token in the url does not match the token assigned to the user, redirect home */
if ($user['token'] !== $token) {
    Application::$app->response->redirect('/');
    return;
}
?>

<div class="max-w-[350px] mx-auto mt-10">
    <section class="p-6 border border-gray-100 shadow-md shadow-gray-200 rounded-lg">
        <h2 class="text-center text-3xl font-medium mb-6">Password reset</h2>

        <!-- ValidationErrors -->
        <?php require_once ROOT_PATH . '/views/partials/validationErrors.php' ?>

        <form action="/password-reset" method="POST">

            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="token" value="<?= $token ?>">

            <div class="mb-5">
                <label for="password" class="block mb-1 text-gray-600 text-xs">
                    Password
                </label>
                <input name="password" placeholder="Enter your new password" type="password" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-6">
                <label for="password_confirm" class="block mb-1 text-gray-600 text-xs">
                    Confirm password
                </label>
                <input name="password_confirm" placeholder="Confirm your new password" type="password" class="w-full text-xs px-4 py-3 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <button type="submit" class="tracking-wide w-full bg-indigo-400 hover:bg-indigo-500 text-white p-3 rounded-md text-xs">
                Reset password
            </button>
        </form>
    </section>
</div>