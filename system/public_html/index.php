<?php
/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

use OpenServer\Controllers\DomainController;
use OpenServer\Controllers\IndexController;
use OpenServer\Controllers\ModuleController;
use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Router\Router;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, GET, HEAD, POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

require_once __DIR__.'/data.php';
require_once __DIR__.'/core/functions.php';

spl_autoload_register(static function ($class) {

    $namespace = dirname($class);

    $localPath = $namespace === 'eftec\bladeone'
        ? str_replace('eftec\bladeone', 'blade', $class)
        : str_replace('OpenServer\\', '', $class);

    if (file_exists(__DIR__.'/core/'.$localPath.'.php')) {
        include __DIR__.'/core/'.$localPath.'.php';
    }

});

try {

    $router = new Router(new Request());

    $router->get('/ping', static fn() => Response::json());

    $router->get('/api/main', IndexController::class);

    $router->post('/api/module', [ModuleController::class, 'all']);

    $router->post('/api/domain/on', [DomainController::class, 'on']);
    $router->post('/api/domain/off', [DomainController::class, 'off']);
    $router->post('/api/domain/create', [DomainController::class, 'create']);
    $router->post('/api/domain/update', [DomainController::class, 'update']);
    $router->post('/api/domain/delete', [DomainController::class, 'delete']);

    $router->resolve();

} catch (Throwable $e) {

    header("{$_SERVER['SERVER_PROTOCOL']} 500 Server Error");
    echo $e->getMessage();

}
