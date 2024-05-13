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
        $domains = Domains::make();
        $apiDomain = $domains->get(API_DOMAIN);

        return Response::json([
            'main'     => [
                'ospVersion' => OSP_VERSION,
                'ospDate'    => OSP_DATE,
                'apiDomain'  => API_DOMAIN,
                'apiEngine'  => $apiDomain->php_engine,
                // 'apiEngine'   => 'php-8.1',//$apiDomain->engine,
                'webApiUrl'  => WEB_API_URL,
                'cliApiUrl'  => CLI_API_URL,

                'totalDomains'    => $domains->count(),
                'disabledDomains' => $domains->countDisabled(),
                'problemDomains'  => $domains->countProblems(),
            ],
            'settings' => IniFile::open('config/program.ini')->get(),
        ]);
    }
}
