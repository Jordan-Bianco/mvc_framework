<?php

use App\controllers\LoginController;
use App\controllers\RegisterController;
use App\controllers\ForgotPasswordController;
use App\controllers\ResetPasswordController;

$app->router->get('/login', [LoginController::class, 'show']);
$app->router->post('/login', [LoginController::class, 'login']);
$app->router->post('/logout', [LoginController::class, 'logout']);

$app->router->get('/register', [RegisterController::class, 'show']);
$app->router->post('/register', [RegisterController::class, 'register']);

$app->router->get('/verify', 'auth/verify');

$app->router->get('/forgot-password', [ForgotPasswordController::class, 'show']);
$app->router->post('/forgot-password', [ForgotPasswordController::class, 'forgot']);

$app->router->get('/password-reset', [ResetPasswordController::class, 'show']);
$app->router->post('/password-reset', [ResetPasswordController::class, 'reset']);
