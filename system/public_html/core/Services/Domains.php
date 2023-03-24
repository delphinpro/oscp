<?php
/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

use OpenServer\DTO\Domain;
use OpenServer\Traits\Makeable;

/**
 * @method static Domains make()
 */
class Domains
{
    use Makeable;

    /** @var \OpenServer\DTO\Domain[] */
    private array $domains;

    public function __construct()
    {
        $this->readConfig();
    }

    /**
     * @return \OpenServer\DTO\Domain[]
     */
    public function getAll(): array
    {
        return $this->domains;
    }

    /**
     * @return \OpenServer\DTO\Domain[]
     */
    public function toArray(bool $filter = false): array
    {
        return array_map(static fn(Domain $d) => $d->toArray(), $this->filterDomains($filter));
    }

    public function getGroups(bool $filter = false): array
    {
        return $this->groupDomains($filter);
    }

    public function has(string $host): bool
    {
        return array_key_exists($host, $this->domains);
    }

    public function get(string $host): Domain
    {
        return $this->domains[$host];
    }

    public function create(string $host, array $data): static
    {
        $this->domains[$host] = new Domain($data);

        return $this;
    }

    public function delete(string $host): static
    {
        unset($this->domains[$host]);

        return $this;
    }

    public function update(string $host, array $data): static
    {
        $this->domains[$host] = new Domain([
            ...($this->domains[$host]->toArray()),
            ...$data,
        ]);

        return $this;
    }

    public function save(): void
    {
        $ini = '';

        foreach ($this->domains as $domain) {
            $cgiDirectory = $this->path($domain->cgi_directory);
            $rootDirectory = $this->path($domain->root_directory);
            $sslCertFile = $this->path($domain->ssl_cert_file);
            $sslKeyFile = $this->path($domain->ssl_key_file);
            $enabled = $domain->enabled ? 'on' : 'off';
            $ssl = $domain->ssl ? 'on' : 'off';
            $selfConfig = $domain->self_config ? 'on' : 'off';
            $projectUseWinEnv = $domain->project_use_win_env ? 'on' : 'off';

            $ini .= PHP_EOL;
            $ini .= <<<DOMAIN
[$domain->host]

aliases         = $domain->aliases
enabled         = $enabled
engine          = $domain->engine
ip              = $domain->ip
log_format      = $domain->log_format
cgi_directory   = $cgiDirectory
root_directory  = $rootDirectory
self_config     = $selfConfig
ssl             = $ssl
ssl_cert_file   = $sslCertFile
ssl_key_file    = $sslKeyFile
project_modules = $domain->project_modules
project_command = $domain->project_command
project_use_win_env = $projectUseWinEnv
DOMAIN;
            $ini .= PHP_EOL;
            foreach ($domain->toArray() as $key => $value) {
                if (!in_array($key, [
                    'host',
                    'aliases',
                    'enabled',
                    'engine',
                    'ip',
                    'log_format',
                    'cgi_directory',
                    'root_directory',
                    'self_config',
                    'ssl',
                    'ssl_cert_file',
                    'ssl_key_file',
                    'project_modules',
                    'project_command',
                    'project_use_win_env',
                ])) {
                    if (is_bool($value)) {
                        $value = $value ? 'on' : 'off';
                    }
                    if (is_null($value)) {
                        $value = 'null';
                    }
                    $ini .= $key.' = '.$value.PHP_EOL;
                }
            }
        }
        file_put_contents(ROOT_DIR.'/config/domains.ini', $ini);
    }

    /**
     * @return \OpenServer\DTO\Domain[]
     */
    private function filterDomains(bool $filter = false): array
    {
        return array_filter($this->domains, static function (Domain $d) use ($filter) {
            if ($d->host === API_DOMAIN) return false;
            if ($filter) return $d->enabled;

            return true;
        });
    }

    /**
     * @return array
     */
    private function groupDomains(bool $filter = false): array
    {
        $result = [];

        foreach ($this->filterDomains($filter) as $item) {
            if (str_contains($item->host, '.')) {
                [, $group] = explode('.', $item->host, 2);
            } else {
                $group = TLD;
            }

            if (!array_key_exists($group, $result)) $result[$group] = [];

            $result[$group][$item->host] = $item;
        }

        $result = $this->groupSubDomains($result);
        //$result = array_filter($result, static fn($d) => !empty($d));

        ksort($result);


        // if (array_key_exists(TLD, $result)) {
        //     $first = array_shift($result);
        //     $result[TLD] = $first;
        // }

        return $result;
    }


    private function groupSubdomains(array $input): array
    {
        $result = [];
        foreach ($input as $groupName => $group) {
            $parts = explode('.', $groupName);
            $result[$groupName] = $group;
            if (count($parts) > 1) {
                if ($input[$parts[1]][$groupName] ?? null) {
                    $result[$groupName][$groupName] = $input[$parts[1]][$groupName] ?? null;
                    // dd($groupName, $parts, $result);
                }
            }
        }
        // dd($input);

        foreach ($result as $groupName => &$group) {
            foreach ($group as $domainName => $domain) {
                if (array_key_exists($domainName, $result) && $domainName !== $groupName) unset($group[$domainName]);
                if ($domainName === TLD) unset($group[$domainName]);
            }
        }
        unset($group);


        foreach ($result as &$group) {
            usort($group, static function (?Domain $a, ?Domain $b) {
                if (!$a || !$b) return 0;
                $d1 = $a->host;
                $d2 = $b->host;
                $l1 = count(explode('.', $d1));
                $l2 = count(explode('.', $d2));
                if ($d1 === $d2) return 0;
                if ($l1 === $l2) return $d1 < $d2 ? -1 : 1;

                return $l1 - $l2;
            });
        }
        unset($group);

        return $result;
    }

    private function readConfig(): void
    {
        $iniSections = parse_ini_file(ROOT_DIR.'/config/domains.ini', true, INI_SCANNER_RAW);

        foreach ($iniSections as $host => &$domain) {
            foreach ($domain as $key => $value) {
                if (in_array(strtolower($value), ['on', 'true', 'yes'])) {
                    $domain[$key] = true;
                }
                if (in_array(strtolower($value), ['off', 'false', 'no', 'none'])) {
                    $domain[$key] = false;
                }
                if (strtolower($value) === 'null') {
                    $domain[$key] = null;
                }
            }
            $this->domains[$host] = new Domain(['host' => $host, ...$domain]);
        }
    }

    private function path(string $path): string
    {
        return toWindowsPath(localPath($path));
    }
}
