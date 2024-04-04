<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\DTO;

use OpenServer\Services\Modules;

/**
 * @property-read string host
 * @property-read string aliases
 * @property-read string engine
 * @property-read string public_dir
 * @property-read string project_home_dir
 * @property-read bool   enabled
 * @property-read string cgi_dir
 * @property-read string ip
 * @property-read string log_format
 * @property-read bool   auto_configure
 * @property-read bool   ssl
 * @property-read string ssl_cert_file
 * @property-read string ssl_key_file
 * @property-read string admin_path
 * @property-read string project_add_modules
 * @property-read string project_add_commands
 * @property-read bool   project_use_sys_env
 */
class Domain
{
    private const DEFAULT_CGI_DIR    = '{root_dir}/home/{host}/cgi-bin';
    private const DEFAULT_PUBLIC_DIR = '{root_dir}/home/{host}/public_html';
    //
    // private const DEFAULT_CERT_FILE = '{root_dir}/user/ssl/default/cert.crt';
    // private const DEFAULT_CERT_KEY  = '{root_dir}/user/ssl/default/cert.key';
    //
    // private const DEFAULT_AUTO_CERT_FILE = '{root_dir}/data/ssl/domains/{host}/cert.crt';
    // private const DEFAULT_AUTO_CERT_KEY  = '{root_dir}/data/ssl/domains/{host}/cert.key';

    protected array $data;

    protected array $rawData;

    protected ?Module $module;

    public function __construct(array $data)
    {
        $this->rawData = $data;
        $this->data = [
            ...$data,
            'host'                 => $data['host'],
            'aliases'              => $data['aliases'] ?? null,
            'engine'               => $data['engine'],
            'public_dir'           => $data['public_dir'] ?? self::DEFAULT_PUBLIC_DIR,
            'project_home_dir'     => $data['project_home_dir'] ?? null,
            'enabled'              => (bool)($data['enabled'] ?? true),
            'cgi_dir'              => $data['cgi_dir'] ?? null,
            'ip'                   => $data['ip'] ?? null,
            'log_format'           => $data['log_format'] ?? null,
            'auto_configure'       => (bool)($data['auto_configure'] ?? true),
            'ssl'                  => (bool)($data['ssl'] ?? false),
            'ssl_cert_file'        => $data['ssl_cert_file'] ?? null,
            'ssl_key_file'         => $data['ssl_key_file'] ?? null,
            'project_add_modules'  => $data['project_add_modules'] ?? null,
            'project_add_commands' => str_replace('&#38;', '&', $data['project_add_commands'] ?? '') ?: null,
            'project_use_sys_env'  => (bool)($data['project_use_sys_env'] ?? false),
        ];

        $this->data['public_dir'] = $this->resolvePath($this->data['public_dir']);
        $this->data['project_home_dir'] = $this->resolvePath($this->data['project_home_dir']);

        $this->module = Modules::make()->get($this->engine);
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function isValidRoot(): bool
    {
        return $this->isValidPath($this->public_dir);
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

    public function update(array $data): void
    {
        $this->data['host'] = $data['host'];
        $data['public_dir'] = templatePath($data['public_dir'] ?? null);
        $data['project_home_dir'] = templatePath($data['project_home_dir'] ?? null);

        $sslEnabled = (bool)($data['ssl'] ?? false);

        $data = array_filter($data, function ($value, $key) use ($sslEnabled) {
            if ($value === null) return false; // Исключаем не заданные параметры
            if ($value === '') return false; // Исключаем не заданные параметры

            // Исключаем параметры со значениями по умолчанию
            if ($key === 'enabled' && $value === true) return false;
            if ($key === 'auto_configure' && $value === true) return false;
            if ($key === 'ssl' && $value === false) return false;
            if ($key === 'project_use_sys_env' && $value === false) return false;
            if ($key === 'ip' && $value === 'auto') return false;
            if ($key === 'log_format' && $value === 'combined') return false;

            $defaultPublicDir = $this->resolvePath(self::DEFAULT_PUBLIC_DIR);
            $defaultCgiDir = $this->resolvePath(self::DEFAULT_CGI_DIR);

            if ($key === 'public_dir' &&
                $defaultPublicDir === $value
            ) {
                return false;
            }
            if ($key === 'public_dir' && $defaultPublicDir === $this->resolvePath($value)) {
                return false;
            }

            if ($key === 'cgi_dir' &&
                !array_key_exists($key, $this->rawData) &&
                $defaultCgiDir === $this->cgi_dir
            ) {
                return false;
            }

            if (!$sslEnabled) {
                if ($key === 'ssl_auto_cert') return false;
                if ($key === 'ssl_cert_file') return false;
                if ($key === 'ssl_key_file') return false;
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        $this->rawData = $data;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function toArray(): array
    {
        return [
            ...$this->data,
            'adminUrl'    => $this->admin_path ? $this->adminUrl() : null,
            'siteUrl'     => $this->siteUrl(),
            'isValidRoot' => $this->isValidRoot(),
            'isAvailable' => $this->isAvailable(),

            'isActive'   => $this->enabled && $this->isAvailable() && $this->isValidRoot(),
            'isProblem'  => $this->enabled && !($this->isAvailable() && $this->isValidRoot()),
            'isDisabled' => !$this->enabled,
        ];
    }

    private function isValidPath(?string $path): bool
    {
        return file_exists($this->resolvePath($path));
    }

    private function resolvePath(?string $path): string
    {
        $replace = [
            '{root_dir}' => ROOT_DIR,
            '{host}'     => $this->host,
            '/'          => '\\',
        ];

        return str_replace(array_keys($replace), array_values($replace), $path ?? '');
    }
}
