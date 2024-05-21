<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\DTO\Domain;
use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Services\Domains;
use OpenServer\Services\Http;
use OpenServer\Services\IniFile;

class SitesController extends Controller
{
    private Domains $domains;

    public function __construct()
    {
        $this->domains = Domains::make();
    }

    public function index(): Response
    {
        $settings = IniFile::open('config/program.ini')->get('menu');
        $isGroupDomains = $settings['show_projects_in_groups'] ?? false;

        $sites = $isGroupDomains
            ? $this->domains->toGroups()
            : $this->domains->toArray();

        return Response::json([
            'grouped' => $isGroupDomains,
            'sites'   => $sites,
        ]);
    }

    public function defaults(): Response
    {
        return Response::json(
            Domain::getDefaults()
        );
    }

    public function store(Request $request): Response
    {
        $host = $request->input('host');
        $data = $request->only(Domain::$params);

        if ($this->domains->has($host)) {
            return Response::json()->status(500)->message("Хост <code>$host</code> уже существует");
        }

        $baseDir = $data['base_dir'];
        $ospDir = $baseDir.'/.osp';

        if (!is_dir($ospDir) && !mkdir($ospDir, true) && !is_dir($ospDir)) {
            return Response::json()->status(500)->message("Не удалось создать каталог <code>$ospDir</code>");
        }

        $defaults = Domain::getDefaults();

        $domain = Domain::create($data);

        $domain->save();

        return Response::json([
        ])->message('Saved');
    }

    public function openConsole(Request $request): Response
    {
        $host = $request->input('host');

        Http::apiCall('cli/'.$host);

        // $file = ROOT_DIR.'/bin/osp__exec.bat';
        // file_put_contents($file, "osp project $host".PHP_EOL);
        //
        // $cmd = str_replace('/', DIRECTORY_SEPARATOR, $file);
        //
        // pclose(popen("start $cmd", 'r'));

        return Response::json();
    }

    private function updateDomain(string $host, array $data, ?string $oldHost = null): Response
    {
        try {

            if (!$this->domains->has($oldHost ?? $host)) {
                throw new \RuntimeException('Ошибка: Хост ['.htmlspecialchars($oldHost ?? $host).'] отсутствует.');
            }

            $this->domains
                ->update($oldHost, $data)
                ->save();

            return Response::json()->message('Сайт сохранён');

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }
}
