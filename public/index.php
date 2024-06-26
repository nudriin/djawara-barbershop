<?php

use Nurdin\Djawara\App\Router;
use Nurdin\Djawara\Controller\AccountController;
use Nurdin\Djawara\Controller\CategoriesController;
use Nurdin\Djawara\Controller\KapstersController;
use Nurdin\Djawara\Controller\OrdersController;
use Nurdin\Djawara\Controller\SchedulesController;
use Nurdin\Djawara\Middleware\AuthAdminMiddleware;
use Nurdin\Djawara\Middleware\AuthMiddleware;

require_once "../vendor/autoload.php";
require_once "../app/App/Router.php";

header('Content-Type: application/json'); // ! Wajib ada biar responsenya bisa berupa json

// ! PUBLIC ROUTES
Router::add("POST", "/api/v1/users", AccountController::class, "register");
Router::add("POST", "/api/v1/users/login", AccountController::class, "login");
// ! ===========

// ! AUTH USER ROUTES
Router::add("GET", "/api/v1/users/current", AccountController::class, "current", [AuthMiddleware::class]);
Router::add("PATCH", "/api/v1/users/current", AccountController::class, "update", [AuthMiddleware::class]);
Router::add("PATCH", "/api/v1/users/current/password", AccountController::class, "password", [AuthMiddleware::class]);
Router::add("DELETE", "/api/v1/users/current/delete", AccountController::class, "remove", [AuthMiddleware::class]);
// ! ===========

// ! AUTH ADMIN ROUTES
Router::add("POST", "/api/v1/admins", AccountController::class, "adminRegister", [AuthAdminMiddleware::class]);
Router::add("POST", "/api/v1/admins/login", AccountController::class, "login");
Router::add("GET", "/api/v1/admins/accounts", AccountController::class, "getAll", [AuthAdminMiddleware::class]);
// ! ===========

// ! KAPSTERS ROUTES
Router::add("POST", "/api/v1/kapsters", KapstersController::class, "add", [AuthAdminMiddleware::class]);
Router::add("GET", "/api/v1/kapsters", KapstersController::class, "getAll");
Router::add("GET", "/api/v1/kapsters/([0-9]+)", KapstersController::class, "getById");
Router::add("PATCH", "/api/v1/kapsters/([0-9]+)", KapstersController::class, "update", [AuthAdminMiddleware::class]);
Router::add("DELETE", "/api/v1/kapsters/([0-9]+)", KapstersController::class, "remove", [AuthAdminMiddleware::class]);
// ! ===========

// ! CATEGORIES ROUTES
Router::add("POST", "/api/v1/categories", CategoriesController::class, "add", [AuthAdminMiddleware::class]);
Router::add("GET", "/api/v1/categories/([0-9]+)", CategoriesController::class, "getById");
Router::add("GET", "/api/v1/categories", CategoriesController::class, "getAll");
Router::add("PATCH", "/api/v1/categories/([0-9]+)", CategoriesController::class, "update", [AuthAdminMiddleware::class]);
Router::add("DELETE", "/api/v1/categories/([0-9]+)", CategoriesController::class, "remove", [AuthAdminMiddleware::class]);
// ! ===========

// ! SCHEDULES ROUTES
Router::add("POST", "/api/v1/schedules", SchedulesController::class, "add");
Router::add("GET", "/api/v1/schedules/([0-9]+)", SchedulesController::class, "getById");
Router::add("GET", "/api/v1/schedules", SchedulesController::class, "getAll");
Router::add("PATCH", "/api/v1/schedules/([0-9]+)", SchedulesController::class, "update", [AuthAdminMiddleware::class]);
Router::add("DELETE", "/api/v1/schedules/([0-9]+)", SchedulesController::class, "remove", [AuthAdminMiddleware::class]);
// ! ===========

// ! ORDERS ROUTES
Router::add("POST", "/api/v1/orders", OrdersController::class, "add");
Router::add("GET", "/api/v1/orders/([0-9]+)", OrdersController::class, "getById");
Router::add("GET", "/api/v1/orders", OrdersController::class, "getAll");
Router::add("GET", "/api/v1/orders/account/([0-9]+)", OrdersController::class, "getAllByAccountId");
Router::add("PATCH", "/api/v1/orders/([0-9]+)", OrdersController::class, "update");
Router::add("DELETE", "/api/v1/orders/([0-9]+)", OrdersController::class, "remove", [AuthAdminMiddleware::class]);
// ! ===========
Router::run();


