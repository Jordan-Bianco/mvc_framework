<?php

// Uncomment this lines to debug the errors
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

require ROOT_PATH . '/app/core/bootstrap.php';
