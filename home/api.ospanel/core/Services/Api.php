<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

use OpenServer\Traits\Makeable;
use RuntimeException;

/**
 * @method static Api make()
 */
class Api
{
    use Makeable;

    private string $apiUrl;

    private string $apiHost;

    public function __construct()
    {
        $this->apiUrl = CLI_API_URL;
        $this->apiHost = str_replace(parse_url(CLI_API_URL, PHP_URL_PATH), '', CLI_API_URL);
    }

    /** @throws \JsonException */
    public function getProjects(): array
    {
        $response = $this->request('projects');

        return json_decode($response, true, flags: JSON_THROW_ON_ERROR);
    }

    private function makeUrl(string $input): string
    {
        return match ($input) {
            'projects' => $this->apiHost.'/getprojects',
            'modules'  => $this->apiHost.'/getmodules',
            default    => $this->apiUrl.'/'.ltrim($input, '/'),
        };
    }

    /**
     * @param  string  $url
     *
     * @return null|array|string|string[]
     * @throws \RuntimeException
     */
    private function request(string $url): array|string|null
    {
        $url1 = $this->makeUrl($url);
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

        return $this->clearString($data);
    }

    private function clearString(string $string): array|string|null
    {
        $string = trim($string);
        $string = str_replace(["\r", ""], "", $string);

        return preg_replace('/\[\d+m/', '', $string);
    }
}
