<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Services\IniFile;

class SettingsController extends Controller
{
    public function edit(): Response
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

    public function save(Request $request): Response
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
