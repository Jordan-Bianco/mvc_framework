<?php

use App\controllers\PageController;

require __DIR__ . '/auth.php';

$app->router->get('/', [PageController::class, 'home']);
