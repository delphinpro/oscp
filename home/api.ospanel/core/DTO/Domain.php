<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\DTO;

use OpenServer\Services\IniFile;
use OpenServer\Services\Modules;

/**
 * @property-read string        host
 * @property-read string        aliases
 * @property-read string        base_dir
 * @property-read bool          defected
 * @property-read string        dip
 * @property-read bool          enabled
 * @property-read string        environment
 * @property-read Array<string> host_aliases
 * @property-read Array<string> host_modules
 * @property-read string        ip
 * @property-read string        nginx_engine
 * @property-read string        node_engine
 * @property-read string        php_engine
 * @property-read string        project_dir
 * @property-read string        project_url
 * @property-read string        public_dir
 * @property-read string        realhost
 * @property-read bool          ssl
 * @property-read string        ssl_cert_file
 * @property-read string        ssl_key_file
 * @property-read string        start_command
 * @property-read string        tag
 * @property-read string        terminal_codepage
 * @property-read string        webhost
 *
 * @property-read string        admin_path
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

    protected array $config;

    protected array $computed = [];

    protected ?Module $phpEngine;

    protected ?Module $nginxEngine;

    public function __construct(array $data)
    {
        $this->computed = $this->normalizeData($data);
        $this->config = IniFile::open($data['base_dir'].'/.osp/project.ini', absolute: true)->get()[$this->host] ?? [];


        $modules = Modules::make();
        $this->phpEngine = $modules->get($this->php_engine);
        $this->nginxEngine = $modules->get($this->nginx_engine);
    }

    public function __get(string $name)
    {
        return $this->computed[$name] ?? $this->config[$name] ?? null;
    }

    public function isValidRoot(): bool
    {
        return $this->isValidPath($this->public_dir);
    }

    public function isAvailable(): bool
    {
        return $this->isReadyPhpEngine()
            && $this->isReadyNginxEngine();
    }

    public function isReadyPhpEngine(): bool
    {
        return !$this->php_engine || $this->phpEngine?->enabled;
    }

    public function isReadyNginxEngine(): bool
    {
        return !$this->nginx_engine || $this->nginxEngine?->enabled;
    }

    public function realIp(): string
    {
        return (string)$this->phpEngine?->ip();
    }

    public function realPort(): string
    {
        return (string)$this->phpEngine?->port();
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

        $this->config = $data;
    }


    public function toArray(): array
    {
        return [
            'host' => $this->host,

            'isActive'           => $this->enabled && $this->isAvailable() && $this->isValidRoot(),
            'isProblem'          => $this->enabled && !($this->isAvailable() && $this->isValidRoot()),
            'isDisabled'         => !$this->enabled,
            'adminUrl'           => $this->admin_path ? $this->adminUrl() : null,
            'siteUrl'            => $this->siteUrl(),
            'isValidRoot'        => $this->isValidRoot(),
            'isAvailable'        => $this->isAvailable(),
            'isReadyPhpEngine'   => $this->isReadyPhpEngine(),
            'isReadyNginxEngine' => $this->isReadyNginxEngine(),

            'config' => [
                ...$this->config,
                'admin_path' => $this->admin_path,
            ],

            'computed' => [
                ...$this->computed,
            ],
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

    private function normalizeData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[$key] = match ($value) {
                'False' => false,
                'True'  => true,
                default => $value,
            };
        }

        return $result;
    }

    private function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    private function isDefault(string $key, $value): bool
    {
        if (array_key_exists($key, $this->computed)) {
            return $this->computed[$key] === $value;
        }

        return false;
    }
}
