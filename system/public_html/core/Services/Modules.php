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

    public function getWebEngines(): array
    {
        return array_values(
            array_filter($this->modules, static fn(Module $module) => $module->compatible === 'Web')
        );
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
        $modules = httpRequest('/modules');
        $modules = explode("\n", $modules);
        $modules = array_filter($modules, static function ($str) {
            return !(
                str_contains($str, 'МОДУЛЬ') ||
                str_contains($str, 'MODULE') ||
                str_contains($str, '—————')
            );
        });
        $this->modules = array_map(static function ($str) {
            $str = trim($str);
            $cols = explode('  ', $str);
            $cols = array_filter($cols, static fn($col) => (bool)$col);
            $cols = array_values(array_map(static fn($col) => trim($col), $cols));
            $name = $cols[0];
            $config = parse_ini_file(ROOT_DIR.'/config/'.$name.'/module.ini');
            $profile = $config['profile'];
            $settings = parse_ini_file(ROOT_DIR.'/config/'.$name.'/'.$profile.'/settings.ini', true, INI_SCANNER_RAW);

            return Module::make(
                name: $name,
                status: $cols[1],
                enabled: in_array($cols[1], ['Включён', 'Enabled', 'Уключаны', 'Включений']),
                init: in_array($cols[1], ['Инициализирован', 'Initialized', 'Ініцыялізаваны', 'Ініціалізовано']),
                version: $cols[2],
                type: $cols[3],
                compatible: $cols[4],
                params: [
                    'ip'   => $settings['main']['ip'] ?? null,
                    'port' => $settings['main']['port'] ?? null,
                ]
            );
        }, array_values($modules));
    }
}
