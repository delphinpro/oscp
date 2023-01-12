<?php

namespace OpenServer\DTO;

use OpenServer\Traits\Makeable;

/**
 * @method static Module make(string $name, string $status, bool $enabled, bool $init, string $version, string $type, string $compatible, string $license)
 */
class Module
{
    use Makeable;

    public function __construct(
        readonly string $name,
        readonly string $status,
        readonly bool $enabled,
        readonly bool $init,
        readonly string $version,
        readonly string $type,
        readonly string $compatible,
        readonly string $license,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'status'     => $this->status,
            'enabled'    => $this->enabled,
            'init'       => $this->init,
            'version'    => $this->version,
            'type'       => $this->type,
            'compatible' => $this->compatible,
            'license'    => $this->license,
        ];
    }
}
