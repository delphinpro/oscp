<?php
/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Response;
use OpenServer\Services\Modules;

class ModuleController extends Controller
{
    public function all(): Response
    {
        try {

            return Response::view('modules', [
                'modules' => Modules::make()->getList(),
            ])->asJson();

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }
}
