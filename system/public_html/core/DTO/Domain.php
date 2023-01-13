<?php

namespace OpenServer\DTO;

use OpenServer\Services\Modules;

/**
 * @property-read string host
 * @property-read string aliases
 * @property-read string engine
 * @property-read string root_directory
 * @property-read bool enabled
 * @property-read string cgi_directory
 * @property-read string ip
 * @property-read string log_directory
 * @property-read string log_format
 * @property-read bool self_config
 * @property-read bool ssl
 * @property-read string ssl_cert_file
 * @property-read string ssl_key_file
 */
class Domain
{
    protected array $data;
    protected ?Module $module;

    public function __construct(array $data)
    {
        $this->data = [
            ...$data,
            'host'           => $data['host'],
            'aliases'        => $data['aliases'] ?? '',
            'engine'         => $data['engine'] ?? 'PHP-8.1',
            'root_directory' => $this->path($data['root_directory']),
            'enabled'        => (bool)($data['enabled'] ?? true),
            'cgi_directory'  => $this->path($data['cgi_directory'] ?? ''),
            'ip'             => $data['ip'] ?? 'auto',
            'log_directory'  => $this->path($data['log_directory'] ?? '{root_dir}/logs/domains'),
            'log_format'     => $data['log_format'] ?? 'combined',
            'self_config'    => (bool)($data['self_config'] ?? false),
            'ssl'            => (bool)($data['ssl'] ?? false),
            'ssl_cert_file'  => $this->path($data['ssl_cert_file'] ?? '{root_dir}/user/ssl/default/cert.crt'),
            'ssl_key_file'   => $this->path($data['ssl_key_file'] ?? '{root_dir}/user/ssl/default/cert.key'),
        ];
        $this->module = Modules::make()->get($this->engine);
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function isValidRoot(): bool
    {
        return file_exists($this->root_directory);
    }

    public function isAvailable(): bool
    {
        return (bool)$this->module?->enabled;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function path(string $path): string
    {
        return toUnixPath(absolutePath($path));
    }
}
