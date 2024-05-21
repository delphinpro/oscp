<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

use OpenServer\DTO\Module;

class Modules
{
    private static ?Modules $instance = null;

    /** @var \OpenServer\DTO\Module[] */
    protected array $modules;

    public function __construct()
    {
        $this->readConfig();
    }

    public static function make(): Modules
    {
        if (!self::$instance) {
            self::$instance = new Modules();
        }

        return self::$instance;
    }

    /**
     * @return \OpenServer\DTO\Module[]
     */
    public function toArray(): array
    {
        return array_map(static fn(Module $module) => $module->toArray(), $this->modules);
    }

    public function getPhpEngines(): array
    {
        $filtered = array_filter($this->modules, static fn(Module $module) => $module->category === 'PHP');

        return array_map(static fn(Module $m) => $m->toArray(), array_values($filtered));
    }

    public function getNginxEngines(): array
    {
        $filtered = array_filter($this->modules, static fn(Module $module) => $module->category === 'Nginx');

        return array_map(static fn(Module $m) => $m->toArray(), array_values($filtered));
    }

    public function get($moduleName): ?Module
    {
        foreach ($this->modules as $module) {
            if ($module->name === $moduleName) {
                return $module;
            }
        }

        return null;
    }

    private function readConfig(): void
    {
        $modules = Http::getJson('getmodules');
        $this->modules = array_map(static function ($item, $name) {
            $profile = $item['profile'];
            $settings = $item['profiles'][$profile];

            return Module::make(
                name: $name,
                enabled: $item['enabled'],
                init: $item['inited'],
                version: $item['version'],
                arch: $item['architecture'],
                category: $item['category'],
                params: [
                    'ip'   => $settings['ip'] ?? null,
                    'port' => $settings['port'] ?? null,
                ]
            );
        }, array_values($modules), array_keys($modules));
    }
}
