<?php

use App\controllers\LoginController;
use App\controllers\PageController;
use App\controllers\RegisterController;

$app->router->get('/', [PageController::class, 'home']);

/** Auth routes */
$app->router->get('/login', [LoginController::class, 'show']);
$app->router->post('/login', [LoginController::class, 'login']);
$app->router->post('/logout', [LoginController::class, 'logout']);

$app->router->get('/register', [RegisterController::class, 'show']);
$app->router->post('/register', [RegisterController::class, 'register']);
$app->router->get('/verify', 'auth/verify');
