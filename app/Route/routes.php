<?php

use App\Controllers\UserController;
use App\Core\Route;

Route::get('/', function () {
    return "Hello World!!!";
});

Route::get('/test/{id}', function ($id) {
    return "Hello World Test $id!!! ";
});

Route::get('/user/{name}', [UserController::class, 'getUsers']);

Route::dispatch();