<?php
/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

namespace OpenServer\DTO;

use OpenServer\Services\Modules;

/**
 * @property-read string host
 * @property-read string aliases
 * @property-read string engine
 * @property-read string root_directory
 * @property-read bool   enabled
 * @property-read string cgi_directory
 * @property-read string ip
 * @property-read string log_format
 * @property-read bool   self_config
 * @property-read bool   ssl
 * @property-read string ssl_cert_file
 * @property-read string ssl_key_file
 * @property-read string admin_path
 * @property-read string project_modules
 * @property-read string project_command
 * @property-read bool   project_use_win_env
 */
class Domain
{
    protected array $data;

    protected ?Module $module;

    public function __construct(array $data)
    {
        $this->data = [
            ...$data,
            'host'                => $data['host'],
            'aliases'             => $data['aliases'] ?? '',
            'engine'              => $data['engine'] ?? 'PHP-8.1',
            'root_directory'      => $this->path($data['root_directory']),
            'enabled'             => (bool)($data['enabled'] ?? true),
            'cgi_directory'       => $this->path($data['cgi_directory'] ?? ''),
            'ip'                  => $data['ip'] ?? 'auto',
            'log_format'          => $data['log_format'] ?? 'combined',
            'self_config'         => (bool)($data['self_config'] ?? false),
            'ssl'                 => (bool)($data['ssl'] ?? false),
            'ssl_cert_file'       => $this->path($data['ssl_cert_file'] ?? '{root_dir}/user/ssl/default/cert.crt'),
            'ssl_key_file'        => $this->path($data['ssl_key_file'] ?? '{root_dir}/user/ssl/default/cert.key'),
            'project_modules'     => $data['project_modules'] ?? '',
            'project_command'     => str_replace('&#38;', '&', $data['project_command'] ?? ''),
            'project_use_win_env' => (bool)($data['project_use_win_env'] ?? false),
        ];
        $this->module = Modules::make()->get($this->engine);
    }

    /** @noinspection MagicMethodsValidityInspection */
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

    public function realIp(): string
    {
        return (string)$this->module?->ip();
    }

    public function realPort(): string
    {
        return (string)$this->module?->port();
    }

    public function siteUrl(): string
    {
        return 'http'.($this->ssl ? 's' : '').'://'.$this->host;
    }

    public function adminUrl(): string
    {
        return 'http'.($this->ssl ? 's' : '').'://'.$this->host.'/'.ltrim($this->admin_path, '/');
    }

    public function console(): string
    {
        return '/project/cli/'.$this->host;
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
