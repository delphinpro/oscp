<?php
/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

use OpenServer\DTO\Module;
use OpenServer\Traits\Makeable;

/**
 * @method static Modules make()
 */
class Modules
{
    use Makeable;

    /** @var \OpenServer\DTO\Module[] */
    protected array $modules;

    public function __construct()
    {
        $this->readConfig();
    }

    /**
     * @return \OpenServer\DTO\Module[]
     */
    public function getList(): array
    {
        return $this->modules;
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
            return !(str_contains($str, 'МОДУЛЬ') || str_contains($str, '—————'));
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
                enabled: $cols[1] === 'Включён',
                init: $cols[1] === 'Инициализирован',
                version: $cols[2],
                type: $cols[3],
                compatible: $cols[4],
                params: [
                    'ip'   => $settings['main']['ip'] ?? null,
                    'port' => $settings['main']['port'] ?? null,
                ]
            );
        }, $modules);
    }
}
