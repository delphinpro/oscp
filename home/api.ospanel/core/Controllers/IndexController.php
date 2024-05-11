<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Services\Domains;
use OpenServer\Services\IniFile;

class IndexController extends Controller
{
    public function __invoke(): Response
    {
        $domain = Domains::make()->get(API_DOMAIN);

        return Response::json([
            'version'     => OSP_VERSION,
            'releaseDate' => OSP_DATE,
            'apiDomain'   => API_DOMAIN,
            'apiEngine'   => $domain->php_engine,
            // 'apiEngine'   => 'php-8.1',//$domain->engine,
            'webApiUrl'   => WEB_API_URL,
            'cliApiUrl'   => CLI_API_URL,
            'settings'    => IniFile::open('config/program.ini')->get(),
        ]);
    }

    public function settings(): Response
    {
        $settings = IniFile::open('config/program.ini')->get();

        return Response::json([
            'settings' => [
                'main'     => $settings['main'] ?? [],
                'menu'     => $settings['menu'] ?? [],
                'projects' => $settings['projects'] ?? [],
            ],
        ]);
    }

    public function saveSettings(Request $request): Response
    {
        $settings = IniFile::open('config/program.ini');
        $input = $request->all();

        $settings
            ->update('main', $input['main'] ?? [])
            ->update('menu', $input['menu'] ?? [])
            ->update('projects', $input['projects'] ?? [])
            ->save();

        return Response::json();
    }
}
