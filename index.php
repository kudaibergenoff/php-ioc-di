<?php

require __DIR__.'/vendor/autoload.php';

use App\Controllers\UserController;
use Dotenv\Dotenv;
use Config\Container;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $controller = (new Container())->get(UserController::class);
    echo $controller->handle();
} catch (Throwable $exception) {
    echo 'Ошибка: ' . $exception->getMessage();
}