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

class SitesController extends Controller
{
    public function __invoke(): Response
    {
        $settings = readIniFile('config/program.ini');
        $isGroupDomains = $settings['menu']['group_projects_by_tld'] ?? false;
        $domains = Domains::make();

        $sites = $isGroupDomains
            ? $domains->toGroups()
            : $domains->toArray();

        return Response::json([
            'grouped' => $isGroupDomains,
            'sites'   => $sites,
        ]);
    }

    public function openConsole(Request $request): Response
    {
        $host = $request->input('host');

        $file = ROOT_DIR.'/bin/osp__exec.bat';
        file_put_contents($file, "osp project $host".PHP_EOL);

        $cmd = str_replace('/', DIRECTORY_SEPARATOR, $file);

        pclose(popen("start $cmd", 'r'));

        return Response::json();
    }
}
