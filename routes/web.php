<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/** @var $router Laravel\Lumen\Routing\Router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([
    'prefix' => 'flag'
], function () use ($router) {
    $router->get('/list', 'FlagController@list');
    $router->post('/add', 'FlagController@add');
    $router->post('/checkIn', 'FlagController@checkIn');
});

$router->group([
   'prefix' => 'category'
], function () use ($router) {
    $router->get('/list', 'CategoryController@list');
    $router->post('/remove', 'CategoryController@remove');
});

$router->group([
    'prefix' => 'task'
], function () use ($router) {
    $router->get('/all', 'TaskController@allTask');
});

$router->group([
    'prefix' => 'user'
], function () use ($router) {
   $router->get('/info', 'UserController@userInfo');
});
