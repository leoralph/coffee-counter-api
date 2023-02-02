<?php

use App\Application;
use App\Controllers\AuthController;
use App\Controllers\CoffeeDrinkController;
use App\Controllers\UserController;
use App\Middlewares\MustHaveToken;
use App\Router;

require_once __DIR__ . "/autoload.php";

// SET THE SECOND PARAMETER TO TRUE FOR DATABASE SETUP (WILL DROP TABLES)
Application::boot(__DIR__, false);

$router = new Router;

$router->post('/users', [UserController::class, 'create']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/users', [UserController::class, 'all'], [MustHaveToken::class]);
$router->get('/users/:iduser', [UserController::class, 'find'], [MustHaveToken::class]);
$router->put('/users/:iduser', [UserController::class, 'update'], [MustHaveToken::class]);
$router->delete('/users/:iduser', [UserController::class, 'delete'], [MustHaveToken::class]);

$router->post('/users/:iduser/drink', [CoffeeDrinkController::class, 'drink'], [MustHaveToken::class]);
$router->get('/users/:iduser/history', [CoffeeDrinkController::class, 'userHistory'], [MustHaveToken::class]);
$router->get('/users/ranking', [CoffeeDrinkController::class, 'userRanking'], [MustHaveToken::class]);

$router->run();
