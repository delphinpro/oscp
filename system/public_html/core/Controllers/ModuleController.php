<?php

namespace OpenServer\Controllers;

use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Services\Modules;

class ModuleController extends Controller
{
    public function init(Request $request): Response
    {
        $module = $request->input('module');
        return $this->action('init', $module);
    }

    public function restart(Request $request): Response
    {
        $module = $request->input('module');
        return $this->action('restart', $module);
    }

    public function on(Request $request): Response
    {
        $module = $request->input('module');
        return $this->action('on', $module);
    }

    public function off(Request $request): Response
    {
        $module = $request->input('module');
        return $this->action('off', $module);
    }

    private function action(string $action, string $module): Response
    {
        try {

            if ($action === 'restart') {
                $res1 = httpRequest('/mod/off/'.$module.'/');
                $res2 = httpRequest('/mod/on/'.$module.'/');
                $res = $res1.'<br>'.$res2;
            } else {
                $res = httpRequest('/mod/'.$action.'/'.$module.'/');
            }

            return Response::view('modules', [
                'modules' => Modules::make()->getList(),
            ])->message($res)->asJson();

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }
}
