<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

namespace OpenServer\Services;

use JsonException;

class Http
{
    public static function getJson(string $url): array
    {
        $url = 'http://ospanel/'.$url;
        $response = file_get_contents($url);

        try {
            $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);

            return self::normalize($data);
        } catch (JsonException $e) {
            return [];
        }
    }

    private static function normalize(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[$key] = match (true) {
                $value === 'False' => false,
                $value === 'True'  => true,
                is_array($value)   => self::normalize($value),
                default            => $value,
            };
        }

        return $result;
    }
}
