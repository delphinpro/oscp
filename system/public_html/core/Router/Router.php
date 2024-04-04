<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Router;

/**
 * @method get(string $route, Callable $callable)
 * @method post(string $route, Callable $callable)
 */
class Router
{
    private Request $request;

    private array $supportedHttpMethods = [
        'GET',
        'POST',
    ];

    private array $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __call($name, $args)
    {
        [$route, $method] = $args;

        if (!in_array(strtoupper($name), $this->supportedHttpMethods, true)) {
            $this->invalidMethodHandler($name);
        }

        $this->routes[strtolower($name)][$this->formatRoute($route)] = $method;
    }

    /**
     * Resolves a route
     */
    public function resolve(): void
    {
        $methodDictionary = $this->routes[strtolower($this->request->requestMethod)];
        $formattedRoute = $this->formatRoute($this->request->requestUri);
        $callable = $methodDictionary[$formattedRoute] ?? null;

        switch (true) {
            case is_string($callable):
                $controller = new $callable();
                echo $controller($this->request);
                break;

            case is_array($callable) && count($callable) === 2:
                $controller = new $callable[0]();
                $method = $callable[1];
                echo $controller->$method($this->request);
                break;

            case is_callable($callable):
                echo $callable($this->request);
                break;

            default:
                $this->defaultRequestHandler();
        }
    }

    private function invalidMethodHandler(string $method): void
    {
        echo Response::json()
            ->headers(["{$this->request->serverProtocol} 405 Method Not Allowed"])
            ->message('405 Method Not Allowed: '.$method)
            ->status(405);
    }

    /**
     * Removes trailing forward slashes from the right of the route.
     */
    private function formatRoute(string $route): string
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }

        return $result;
    }

    private function defaultRequestHandler(): void
    {
        echo Response::json()
            ->headers(["{$this->request->serverProtocol} 404 Not Found"])
            ->message('404 Not Found: '.$this->request->requestUri)
            ->status(404);
    }
}
