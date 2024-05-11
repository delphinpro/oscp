<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Router;

/**
 * @property-read string $httpHost
 * @property-read string $httpUserAgent
 * @property-read string $httpAccept
 * @property-read string $httpReferer
 * @property-read string $httpAcceptEncoding
 * @property-read string $httpAcceptLanguage
 * @property-read string $httpCookie
 * @property-read string $serverName
 * @property-read string $serverAddr
 * @property-read string $serverPort
 * @property-read string $remoteAddr
 * @property-read string $documentRoot
 * @property-read string $requestScheme
 * @property-read string $scriptFilename
 * @property-read string $serverProtocol
 * @property-read string $requestMethod
 * @property-read string $queryString
 * @property-read string $requestUri
 * @property-read string $scriptName
 * @property-read string $phpSelf
 * @property-read string $requestTime
 */
class Request
{
    protected array $requestVariables = [];

    protected ?array $body = null;

    protected ?array $queryParams = null;

    public function __construct()
    {
        $this->bootstrapSelf();
        $this->parseBody();
    }

    public function __get(string $name)
    {
        return $this->requestVariables[$name] ?? null;
    }

    public function get(string $key, string|int|bool|null $default = null): string|int|bool|null
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function input(string $key, string|int|bool|null $default = null): string|int|bool|null
    {
        return $this->body[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->body;
    }

    public function except(array $keys): array
    {
        return array_filter(
            $this->body,
            static fn($key) => !in_array($key, $keys, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return $this->requestVariables;
    }

    private function bootstrapSelf(): void
    {
        foreach ($_SERVER as $key => $value) {
            $this->requestVariables[$this->toCamelCase($key)] = $value;
        }
        [$requestUri] = explode('?', $this->requestUri);
        $this->requestVariables['requestUri'] = $requestUri;
        $this->queryParams = $_GET;
    }

    private function parseBody(): void
    {
        if ($this->requestMethod === "POST") {
            $this->body = [];
            foreach ($_POST as $key => $value) {
                $this->body[$key] = $this->castInputValue($value);
            }
        }
    }

    private function castInputValue(mixed $value)
    {
        if (is_array($value)) {
            return array_map(fn($value) => $this->castInputValue($value), $value);
        }

        return match (true) {
            $value === 'true'  => true,
            $value === 'false' => false,
            $value === 'null'  => null,
            is_numeric($value) => (int)$value,
            default            => $value,
        };

    }

    private function toCamelCase($string): array|string
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);

        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }
}
