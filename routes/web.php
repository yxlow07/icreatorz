<?php

use app\Controllers\AdminController;
use app\Controllers\AuthController;
use app\Controllers\UserController;
use core\Router\RoutesCollector;

$routes = new RoutesCollector();

$guest = 'guest';
$user = 'auth-user';
$admin = 'auth-admin';

$routes->GET("/", [UserController::class, 'renderHome']);
$routes->GET("/message", [UserController::class, 'web']);

// UserModel profiles
$routes->GETPOST('/register',[AuthController::class, 'register'])->only($guest);
$routes->GETPOST('/login', [AuthController::class, 'login'])->only($guest);
$routes->GETPOST('/logout', [AuthController::class, 'logout'])->only('auth');
$routes->GETPOST('/profile', [UserController::class, 'profile'])->only($user);

// UserModel static pages
$routes->GET('/forgot_password', 'forgot_password')->only($guest);

//Admin pages
$routes->GET('/crud_users', [AdminController::class, 'list_users'])->only($admin);
$routes->GETPOST('/add_admin', [AdminController::class, 'under_work'])->only($admin);
$routes->GETPOST('/users/create', [AdminController::class, 'createUsers'])->only($admin);
$routes->GETPOST('/users/{id}/{action}', [AdminController::class, 'crud_users'])->only($admin);
$routes->GET('/notify', [AdminController::class, 'emit'])->only($admin);
$routes->PUT('/notify', [AdminController::class, 'emitMessage'])->only($admin);

return $routes;