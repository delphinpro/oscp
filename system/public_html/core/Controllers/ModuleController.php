<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Response;
use OpenServer\Services\Modules;

class ModuleController extends Controller
{
    public function __invoke(): Response
    {
        try {

            return Response::json([
                'modules' => Modules::make()->toArray(),
            ])->asJson();

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }

    public function engines(): Response
    {
        $modules = Modules::make();

        return Response::json([
            'engines' => $modules->getWebEngines(),
        ]);
    }
}
