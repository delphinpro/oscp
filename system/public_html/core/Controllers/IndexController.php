<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Response;
use OpenServer\Services\Domains;

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
            'settings'    => readIniFile('config/program.ini'),
        ]);
    }

    public function restart(): Response
    {
        if (!file_exists($file = ROOT_DIR.'/bin/osp__restart.bat')) {
            file_put_contents($file, 'osp exit & ospanel'.PHP_EOL);
        }

        $cmd = str_replace('/', DIRECTORY_SEPARATOR, $file);

        pclose(popen("start /B $cmd", 'r'));

        return Response::json();
    }
}
