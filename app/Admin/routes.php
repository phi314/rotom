<?php

use Illuminate\Routing\Router;

Route::group([
    'prefix'        => config('admin.prefix'),
    'namespace'     => Admin::controllerNamespace(),
    'middleware'    => ['web', 'admin'],
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('user', UserController::class);
    $router->resource('wash', WashController::class);
    $router->resource('category', CategoryController::class);
    $router->resource('item', ItemController::class);
    $router->resource('invoice', InvoiceController::class);

    $router->get('invoice/{id}/pdf', 'InvoiceController@pdf');

    /**
     * API json
     */
    $router->get('/api/items', 'ItemController@items');
    $router->get('/api/users', 'UserController@users');
    $router->get('/api/washes', 'WashController@washes');

});
