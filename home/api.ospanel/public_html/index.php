<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer;

use OpenServer\Controllers\FilesController;
use OpenServer\Controllers\IndexController;
use OpenServer\Controllers\ModuleController;
use OpenServer\Controllers\SitesController;
use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Router\Router;


require_once __DIR__.'/../data.php';
require_once __DIR__.'/../core/functions.php';

spl_autoload_register(static function ($class) {

    $localPath = str_replace('OpenServer\\', '', $class);

    if (file_exists(__DIR__.'/../core/'.$localPath.'.php')) {
        include __DIR__.'/../core/'.$localPath.'.php';
    }

});

try {

    $router = new Router(new Request());

    $router->get('/ping', static fn() => Response::json());

    $router->get('/api/main', IndexController::class);

    $router->get('/api/modules', ModuleController::class);
    $router->get('/api/modules/engines', [ModuleController::class, 'engines']);

    $router->get('/api/sites', SitesController::class);
    $router->post('/api/sites/console', [SitesController::class, 'openConsole']);
    $router->post('/api/sites/data', [SitesController::class, 'getSite']);
    $router->post('/api/sites/save', [SitesController::class, 'save']);
    $router->post('/api/sites/delete', [SitesController::class, 'delete']);

    $router->post('/api/fs', FilesController::class);
    $router->post('/api/fs/create', [FilesController::class, 'create']);

    $router->resolve();

} catch (\Throwable $e) {

    header("{$_SERVER['SERVER_PROTOCOL']} 500 Server Error");
    echo $e->getMessage();

}
