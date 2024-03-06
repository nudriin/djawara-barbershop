<?php

use Nurdin\Djawara\App\Router;
use Nurdin\Djawara\Controller\AccountController;
use Nurdin\Djawara\Middleware\AuthAdminMiddleware;
use Nurdin\Djawara\Middleware\AuthMiddleware;

require_once "../vendor/autoload.php";
require_once "../app/App/Router.php";

header('Content-Type: application/json'); // ! Wajib ada biar responsenya bisa berupa json

Router::add("POST", "/api/v1/users", AccountController::class, "register");
Router::add("POST", "/api/v1/users/login", AccountController::class, "login");

Router::add("GET", "/api/v1/users/current", AccountController::class, "current", [AuthMiddleware::class]);
Router::add("PATCH", "/api/v1/users/current", AccountController::class, "update", [AuthMiddleware::class]);
Router::add("PATCH", "/api/v1/users/current/password", AccountController::class, "password", [AuthMiddleware::class]);
Router::add("DELETE", "/api/v1/users/current/delete", AccountController::class, "remove", [AuthMiddleware::class]);

Router::add("POST", "/api/v1/admins", AccountController::class, "adminRegister", [AuthAdminMiddleware::class]);
Router::add("POST", "/api/v1/admins/login", AccountController::class, "login");


Router::run();


