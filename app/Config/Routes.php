<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::authenticate');
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->post('logout', 'AuthController::logout');
    foreach (['products' => 'ProductController', 'customers' => 'CustomerController', 'staff' => 'StaffController'] as $path => $controller) {
        $routes->get($path, $controller . '::index');
        $routes->get($path . '/new', $controller . '::create');
        $routes->post($path, $controller . '::store');
        $routes->get($path . '/(:num)/edit', $controller . '::edit/$1');
        $routes->post($path . '/(:num)', $controller . '::update/$1');
        $routes->post($path . '/(:num)/delete', $controller . '::delete/$1');
    }
    $routes->get('sales/new', 'SalesController::create');
    $routes->post('sales', 'SalesController::store');
    $routes->get('sales', 'SalesController::index');
    $routes->get('media/(:segment)', 'MediaController::show/$1');
});

