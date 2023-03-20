<?php
/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

namespace OpenServer\Controllers;

use OpenServer\Router\Request;
use OpenServer\Router\Response;
use OpenServer\Services\Domains;

class DomainController extends Controller
{
    private Domains $domains;

    public function __construct()
    {
        $this->domains = Domains::make();
    }

    public function on(Request $request): Response
    {
        return $this->updateDomain($request->input('host'), [
            'enabled' => true,
        ]);
    }

    public function off(Request $request): Response
    {
        return $this->updateDomain($request->input('host'), [
            'enabled' => false,
        ]);
    }

    public function update(Request $request): Response
    {
        return $this->updateDomain(
            $request->input('host'),
            $request->except([
                'action',
                'old_host',
            ]),
            $request->input('old_host') ?: null
        );
    }

    public function create(Request $request): Response
    {
        try {
            $host = $request->input('host');

            if ($this->domains->has($host)) {
                throw new \RuntimeException('Ошибка: Хост ['.htmlspecialchars($host).'] уже существует.');
            }

            $data = $request->except([
                'action',
                'old_host',
            ]);

            $this->domains
                ->create($host, [
                    ...$data,
                ])
                ->save();

            return Response::json([
                'domainsList' => $this->domains->toArray(),
                'domains'     => (string)Response::view('domains', [
                    'domains' => $this->domains->getGroups(),
                ]),
                'sites'       => (string)Response::view('sites', [
                    'sites' => $this->domains->getGroups(filter: true),
                ]),
            ]);

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }

    public function delete(Request $request): Response
    {
        try {
            $host = $request->input('host');

            if (!$this->domains->has($host)) {
                throw new \RuntimeException('Ошибка: Хост ['.htmlspecialchars($host).'] отсутствует.');
            }

            $this->domains
                ->delete($host)
                ->save();

            return Response::json([
                'domainsList' => $this->domains->toArray(),
                'domains'     => (string)Response::view('domains', [
                    'domains' => $this->domains->getGroups(),
                ]),
                'sites'       => (string)Response::view('sites', [
                    'sites' => $this->domains->getGroups(filter: true),
                ]),
            ]);

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }

    private function updateDomain(string $host, array $data, ?string $oldHost = null): Response
    {
        try {

            if (!$this->domains->has($oldHost ?? $host)) {
                throw new \RuntimeException('Ошибка: Хост ['.htmlspecialchars($oldHost ?? $host).'] отсутствует.');
            }

            if ($oldHost) {
                $old = $this->domains->get($oldHost);

                $this->domains
                    ->delete($oldHost)
                    ->create($host, [
                        ...$old->toArray(),
                        ...$data,
                    ])
                    ->save();
            } else {
                $this->domains
                    ->update($host, $data)
                    ->save();
            }


            return Response::json([
                'domainsList' => $this->domains->toArray(),
                'domains'     => (string)Response::view('domains', [
                    'domains' => $this->domains->getGroups(),
                ]),
                'sites'       => (string)Response::view('sites', [
                    'sites' => $this->domains->getGroups(filter: true),
                ]),
            ]);

        } catch (\Exception $e) {

            return Response::json()
                ->status(500)
                ->message($e->getMessage());

        }
    }
}
