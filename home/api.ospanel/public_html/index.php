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
use OpenServer\Controllers\SettingsController;
use OpenServer\Controllers\SitesController;
use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Router\Router;

error_reporting(E_ALL);
ini_set('display_errors', 1);


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

    $router->get('/main', IndexController::class);

    $router->get('/settings', [SettingsController::class, 'edit']);
    $router->post('/settings', [SettingsController::class, 'save']);

    $router->get('/modules', ModuleController::class);
    $router->get('/modules/engines', [ModuleController::class, 'engines']);

    $router->get('/sites', [SitesController::class, 'index']);
    $router->get('/sites/defaults', [SitesController::class, 'defaults']);
    $router->get('/sites/edit', [SitesController::class, 'edit']);
    $router->post('/sites/store', [SitesController::class, 'store']);
    $router->post('/sites/save', [SitesController::class, 'save']);
    $router->post('/sites/delete', [SitesController::class, 'delete']);
    $router->post('/sites/console', [SitesController::class, 'openConsole']);

    $router->post('/fs', FilesController::class);
    $router->post('/fs/create', [FilesController::class, 'create']);

    $router->resolve();

} catch (\Throwable $e) {

    header("{$_SERVER['SERVER_PROTOCOL']} 500 Server Error");
    echo $e->getMessage();

}
