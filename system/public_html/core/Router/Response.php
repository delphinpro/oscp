<?php
/*
 * Web OSP by delphinpro
 * Copyright (c) 2023-2024.
 * Licensed under MIT License
 */

namespace OpenServer\Router;

class Response
{
    protected string|array|null $content = null;

    protected int $status = 200;

    protected ?string $message = null;

    protected bool $json = false;

    protected array $headers = [];

    public static function json(array|string|null $data = null): static
    {
        return (new static())
            ->setContent($data)
            ->asJson();
    }

    /**
     * @throws \JsonException
     */
    public function __toString(): string
    {
        foreach ($this->headers as $header) {
            header($header);
        }

        if ($this->json) {
            header('Content-Type: application/json');

            return json_encode([
                'status'  => $this->status,
                'message' => $this->message,
                'payload' => $this->content,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        }

        header('Content-Type: text/html');

        return $this->content;
    }

    public function status(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function headers(array $headers): static
    {
        $this->headers = array_merge(
            $this->headers,
            $headers,
        );

        return $this;
    }


    public function setContent(string|array|null $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function asJson(): static
    {
        $this->json = true;

        return $this;
    }
}
