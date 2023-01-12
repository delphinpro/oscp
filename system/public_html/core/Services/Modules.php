<?php

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

    private function readConfig(): void
    {
        $modules = httpRequest('/mod/list/all/');
        $modules = explode("\n", $modules);
        $modules = array_filter($modules, static function ($str) {
            return !(str_contains($str, 'МОДУЛЬ') || str_contains($str, '—————'));
        });
        $this->modules = array_map(static function ($str) {
            $str = trim($str);
            $cols = explode('  ', $str);
            $cols = array_filter($cols, static fn($col) => (bool)$col);
            $cols = array_values(array_map(static fn($col) => trim($col), $cols));
            return Module::make(
                name: $cols[0],
                status: $cols[1],
                enabled: $cols[1] === 'Включён',
                init: $cols[1] === 'Инициализирован',
                version: $cols[2],
                type: $cols[3],
                compatible: $cols[4],
                license: $cols[5],
            );
        }, $modules);
    }
}
