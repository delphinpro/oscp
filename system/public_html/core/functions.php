<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

use JetBrains\PhpStorm\NoReturn;

const TLD = 'TLD';

function dump($var, $level = 0): void
{
    switch (true) {
        case is_array($var):
            echo '<span style="color:#aaa">array ('.count($var).')</span> ['.PHP_EOL;
            $keys = array_keys($var);
            $max = array_reduce($keys, static fn($carry, $key) => max($carry, strlen($key)), 0);
            foreach ($var as $k => $v) {
                $offset = $max - strlen($k) + 1;
                echo str_repeat('    ', $level + 1).'\''.$k.'\''.str_repeat(' ', $offset).'=> ';
                if ($level > 7) {
                    echo '** MAX DEPTH **'.PHP_EOL;
                } else {
                    dump($v, $level + 1);
                }
            }
            echo str_repeat('    ', $level).']'.PHP_EOL;
            break;

        case is_object($var):
            if ($var::class === 'Closure') {
                echo '<span style="color:#aaa">Closure() { }</span>'.PHP_EOL;
            } else {
                echo '<span style="color:#aaa">object ('.$var::class.')</span> ['.PHP_EOL;
                $var = (array)$var;
                $keys = array_keys($var);
                $max = array_reduce($keys, static fn($carry, $key) => max($carry, strlen($key)), 0);
                foreach ($var as $k => $v) {
                    $offset = $max - strlen($k) + 1;
                    echo str_repeat('    ', $level + 1).'<i>'.$k.'</i>'.str_repeat(' ', $offset).'=> ';
                    if ($level > 2) {
                        echo '**RECURSION**'.PHP_EOL;
                    } else {
                        dump($v, $level + 1);
                    }
                }
                echo str_repeat('    ', $level).']'.PHP_EOL;
            }
            break;

        case is_string($var):
            echo '<span style="color:#aaa">string('.mb_strlen($var).')</span> ';
            var_export($var);
            echo PHP_EOL;
            break;

        case is_scalar($var):
            echo '<span style="color:#aaa">'.gettype($var).'(</span>';
            var_export($var);
            echo '<span style="color:#aaa">)</span>';
            echo PHP_EOL;
            break;

        default:
            var_export($var);
            echo PHP_EOL;
    }
}

#[NoReturn]
function dd(...$vars): void
{
    foreach ($vars as $var) {
        echo '<pre style="font-size:1rem">';
        dump($var);
        echo '</pre>';
    }
    exit;
}

function clearString(string $string): array|string|null
{
    $string = trim($string);
    $string = str_replace(["\r", ""], "", $string);

    return preg_replace('/\[\d+m/', '', $string);
}

/**
 * @param $url
 *
 * @return null|array|string|string[]
 * @throws \RuntimeException
 */
function httpRequest($url): array|string|null
{
    $url1 = CLI_API_URL.'/'.ltrim($url, '/');
    $ch = curl_init($url1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);

    if ($data === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException($error.' '.$url1);
    }

    curl_close($ch);

    return clearString($data);
}

function templatePath(?string $path): ?string
{
    if ($path === null) return null;

    return str_replace(str_replace('/', '\\', ROOT_DIR), '{root_dir}', $path);
}
