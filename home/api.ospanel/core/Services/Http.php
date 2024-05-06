<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

class Http
{
    /**
     * @throws \JsonException
     */
    public static function get(string $url): array
    {
        $url = 'http://ospanel/'.$url;
        $response = file_get_contents($url);

        return json_decode($response, true, flags: JSON_THROW_ON_ERROR);
    }
}
