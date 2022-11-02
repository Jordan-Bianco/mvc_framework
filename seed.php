<?php

use App\core\Application;
use App\core\Config;

define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

$app = new Application(new Config($_ENV));

if (isset($argv[1]) && $argv[1] === 'fresh') {
    $app->db->truncateDatabaseTables();
}

echo 'seeding data...' . PHP_EOL;

$password = password_hash('password', PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

$query = "
    INSERT INTO users(username, email, password, verified, token)
    VALUES('user', 'user@mail.com', '$password', 1, '$token');
    ";

$app->db->pdo->exec($query);

echo 'seeding completed';
