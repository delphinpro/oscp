<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

use JsonException;
use OpenServer\DTO\Domain;
use OpenServer\Traits\Makeable;

/**
 * @method static Domains make()
 */
class Domains
{
    use Makeable;

    /** @var \OpenServer\DTO\Domain[] */
    private array $domains = [];

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
    public function toArray(): array
    {
        $domains = $this->filterDomains();

        usort($domains, static function (Domain $a, Domain $b) {
            if ($a->host === $b->host) return 0;

            return $a->host < $b->host ? -1 : 1;
        });

        return array_map(static fn(Domain $d) => $d->toArray(), $domains);
    }

    public function toGroups(): array
    {
        $result = [];

        foreach ($this->filterDomains() as $item) {
            if (str_contains($item->host, '.')) {
                [, $group] = explode('.', $item->host, 2);
            } else {
                $group = TLD;
            }

            if (!array_key_exists($group, $result)) $result[$group] = [];

            $result[$group][$item->host] = $item;
        }

        $result = $this->groupSubDomains($result);

        ksort($result);

        return $result;
    }

    public function has(string $host): bool
    {
        return array_key_exists($host, $this->domains);
    }

    public function get(string $host): Domain
    {
        return $this->domains[$host];
    }

    public function update(string $oldHost, array $data): static
    {
        $domain = new Domain($this->domains[$oldHost]->getRawData());
        $domain->update($data);
        unset($this->domains[$oldHost]);
        $this->domains[$domain->host] = $domain;

        return $this;
    }

    public function save(): void
    {
        $iniData = [];

        foreach ($this->domains as $domain) {
            $iniData[$domain->host] = array_filter(
                $domain->getRawData(),
                static fn($value, $key) => $key !== 'host' && $value !== null,
                ARRAY_FILTER_USE_BOTH
            );
        }

        IniFile::open('config/domains.ini')->set($iniData)->write(20);
    }

    public function count(): int
    {
        return count($this->filterDomains());
    }

    public function countDisabled(): int
    {
        return count(
            array_filter($this->filterDomains(), static function (Domain $d) {
                return $d->enabled === false;
            })
        );
    }

    public function countProblems(): int
    {
        return count(
            array_filter($this->filterDomains(), static function (Domain $d) {
                return !$d->isAvailable() || !$d->isValidRoot();
            })
        );
    }

    /**
     * @return \OpenServer\DTO\Domain[]
     */
    private function filterDomains(): array
    {
        return array_filter($this->domains, static fn(Domain $d) => $d->host !== API_DOMAIN);
    }

    private function groupSubdomains(array $input): array
    {
        $result = [];
        foreach ($input as $groupName => $group) {
            $parts = explode('.', $groupName);
            $result[$groupName] = $group;
            if ((count($parts) > 1) && isset($input[$parts[1]][$groupName])) {
                $result[$groupName][$groupName] = $input[$parts[1]][$groupName] ?? null;
            }
        }

        foreach ($result as $groupName => &$group) {
            foreach ($group as $domainName => $domain) {
                if (array_key_exists($domainName, $result) && $domainName !== $groupName) unset($group[$domainName]);
                if ($domainName === TLD) unset($group[$domainName]);
            }
        }
        unset($group);


        foreach ($result as $name => $group) {
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

            $hasActive = false;
            $group = array_map(static function (Domain $d) use (&$hasActive) {
                if ($d->enabled && $d->isAvailable() && $d->isValidRoot()) $hasActive = true;

                return $d->toArray();
            }, $group);

            $result[$name] = [
                'name'      => $name,
                'hasActive' => $hasActive,
                'domains'   => $group,
            ];
        }

        return array_filter($result, static fn(array $group) => count($group['domains']));
    }

    private function readConfig(): void
    {
        try {
            $domainsData = Api::make()->getProjects();
        } catch (JsonException $e) {
            $domainsData = [];
        }

        foreach ($domainsData as $host => $domain) {
            $this->domains[$host] = new Domain(['host' => $host, ...$domain]);
        }
    }
}
