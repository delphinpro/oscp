<?php

namespace OpenServer\Controllers;

use OpenServer\Router\Response;
use OpenServer\Services\Domains;
use OpenServer\Services\Modules;

class IndexController extends Controller
{
    public function __invoke(): Response
    {
        $domains = Domains::make();
        $modules = Modules::make();

        return Response::json([
            'domains' => $domains->toArray(),
            'main'    => (string)Response::view('app', [
                'sites'   => $domains->getGroups(filter: true),
                'domains' => $domains->getGroups(),
                'modules' => $modules->getList(),
            ]),
        ]);
    }
}
