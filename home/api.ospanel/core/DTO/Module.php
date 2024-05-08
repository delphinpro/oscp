<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\DTO;

use OpenServer\Traits\Makeable;

/**
 * @method static Module make(string $name, bool $enabled, bool $init, string $version, string $arch, string $category, array $params)
 */
class Module
{
    use Makeable;

    public function __construct(
        readonly string $name,
        readonly bool $enabled,
        readonly bool $init,
        readonly string $version,
        readonly string $arch,
        readonly string $category,
        readonly array $params = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'alt_name' => $this->altName(),
            'enabled'  => $this->enabled,
            'init'     => $this->init,
            'version'  => $this->version,
            'arch'     => $this->arch,
            'category' => $this->category,

            'ip'   => $this->ip(),
            'port' => $this->port(),
        ];
    }

    public function altName(): string
    {
        $name = str_replace('-', ' ', $this->name);
        if (str_starts_with($name, 'PHP')) {
            if (str_contains($name, 'FCGI')) {
                return str_replace(' FCGI', '', $name).' FastCGI';
            }

            return $name.' Apache';
        }

        return $name;
    }

    public function ip(): ?string
    {
        return $this->params['ip'] ?? null;
    }

    public function port(): ?string
    {
        return $this->params['port'] ?? null;
    }
}
