<?php

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

    $router->get('/', static fn() => Response::json());

    $router->get('/main', IndexController::class);

    $router->post('/module/init', [ModuleController::class, 'init']);
    $router->post('/module/restart', [ModuleController::class, 'restart']);
    $router->post('/module/on', [ModuleController::class, 'on']);
    $router->post('/module/off', [ModuleController::class, 'off']);

    $router->post('/domain/on', [DomainController::class, 'on']);
    $router->post('/domain/off', [DomainController::class, 'off']);
    $router->post('/domain/create', [DomainController::class, 'create']);
    $router->post('/domain/update', [DomainController::class, 'update']);
    $router->post('/domain/delete', [DomainController::class, 'delete']);

} catch (Exception $e) {

    echo $e->getMessage();

}
