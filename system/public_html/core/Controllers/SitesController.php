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
    private Domains $domains;

    public function __construct()
    {
        $this->domains = Domains::make();
    }

    public function __invoke(): Response
    {
        $settings = readIniFile('config/program.ini');
        $isGroupDomains = $settings['menu']['group_projects_by_tld'] ?? false;

        $sites = $isGroupDomains
            ? $this->domains->toGroups()
            : $this->domains->toArray();

        return Response::json([
            'grouped' => $isGroupDomains,
            'sites'   => $sites,
        ]);
    }

    public function getSite(Request $request): Response
    {
        $host = $request->input('host');
        if (!$this->domains->has($host)) {
            return Response::json()->status(404)->message('Хост не найден');
        }

        $site = $this->domains->get($host)->toArray();

        return Response::json([
            'host' => $host,
            'site' => $site,
        ]);
    }

    public function save(Request $request): Response
    {
        $oldHost = $request->input('old_host');
        $host = $request->input('host');

        $data = $request->except([
            'old_host',
            'adminUrl',
            'siteUrl',
            'isValidRoot',
            'isAvailable',
            'isActive',
            'isProblem',
            'isDisabled',
        ]);

        if (!$oldHost) {
            if ($this->domains->has($host)) {
                return Response::json()->status(500)->message("Хост <code>$host</code> уже существует");
            }

            $this->domains
                ->create($host, $data)
                ->save();

            return Response::json()->message('Сайт создан');
        }

        return $this->updateDomain(
            $request->input('host'),
            $data,
            $request->input('old_host') ?: null
        );
    }

    public function delete(Request $request): Response
    {
        try {
            $host = $request->input('host');

            if (!$this->domains->has($host)) {
                return Response::json()->status(404)->message('Хост не найден');
            }

            $this->domains
                ->delete($host)
                ->save();

            return Response::json()->message('Сайт удалён');

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
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
