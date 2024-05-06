<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

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
            'apiEngine'   => $domain->engine,
            'webApiUrl'   => WEB_API_URL,
            'cliApiUrl'   => CLI_API_URL,
            'settings'    => IniFile::open('config/program.ini')->get(),
        ]);
    }
}
