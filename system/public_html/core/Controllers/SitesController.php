<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

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
}
