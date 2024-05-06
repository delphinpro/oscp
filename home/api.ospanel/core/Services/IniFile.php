<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

class IniFile
{
    private string $filename;

    private array $data = [];

    public function __construct(string $filename, bool $absolute = false)
    {
        $this->filename = $absolute
            ? $filename
            : ROOT_DIR.'/'.ltrim($filename, '/');
    }

    public static function open(string $filename, bool $absolute = false): IniFile
    {
        return (new self($filename, $absolute))
            ->read();
    }

    public static function castValue(string $value): float|bool|int|string|null
    {
        if (in_array(strtolower($value), ['on', 'true', 'yes'])) {
            return true;
        }
        if (in_array(strtolower($value), ['off', 'false', 'no'])) {
            return false;
        }
        if (strtolower($value) === 'null') {
            return null;
        }
        if (is_numeric($value)) {
            if (str_contains($value, '.')) {
                return (float)$value;
            }

            return (int)$value;
        }

        return $value;
    }

    public static function iniValue(float|bool|int|string|null $value): string
    {
        return match (true) {
            is_bool($value)   => $value ? 'on' : 'off',
            is_null($value)   => 'null',
            is_string($value) => str_replace('\\\\', '\\', $value),
            default           => (string)$value
        };
    }

    public function get(): array
    {
        return $this->data;
    }

    public function set(array $data): IniFile
    {
        $this->data = $data;

        return $this;
    }

    public function read(): IniFile
    {
        if (!file_exists($this->filename)) {
            return $this;
        }

        $iniSections = parse_ini_file($this->filename, true, INI_SCANNER_RAW);
        $this->data = [];

        foreach ($iniSections as $section => $params) {
            $this->data[$section] = [];
            foreach ($params as $key => $value) {
                $this->data[$section][$key] = self::castValue($value);
            }
        }

        return $this;
    }

    public function write(int $keyLength = 0): void
    {
        $ini = '';

        $length = max($this->maxLengthKey($this->data), $keyLength);

        foreach ($this->data as $k => $item) {
            if (is_array($item)) {
                $ini .= PHP_EOL.'['.$k.']'.PHP_EOL;
                $len = max($this->maxLengthKey($item), $keyLength);
                foreach ($item as $key => $value) {
                    $ini .= $key.str_repeat(' ', $len - strlen($key)).' = '.self::iniValue($value).PHP_EOL;
                }
            } else {
                $ini .= $k.str_repeat(' ', $length - strlen($k)).' = '.self::iniValue($item).PHP_EOL;
            }
        }

        file_put_contents($this->filename, $ini);
    }

    private function maxLengthKey(array $array): int
    {
        return array_reduce(
            array_keys($array),
            static fn($carry, $key) => max($carry, strlen($key)),
            0
        );
    }
}
