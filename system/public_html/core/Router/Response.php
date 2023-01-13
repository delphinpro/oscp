<?php

namespace OpenServer\Router;

use eftec\bladeone\BladeOne;

class Response
{
    protected string|array|null $content = null;
    protected int $status = 200;
    protected ?string $message = null;
    protected bool $json = false;

    public static function view(string $view, array $data): static
    {
        $blade = new BladeOne(
            templatePath: dirname(__DIR__).'/views',
            compiledPath: dirname(__DIR__).'/.cache',
            mode: BladeOne::MODE_DEBUG
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        return (new static())
            ->setContent($blade->run($view, $data));
    }

    public static function json(array|string|null $data = null): static
    {
        return (new static())
            ->setContent($data)
            ->asJson();
    }

    public function __toString(): string
    {
        if ($this->json) {
            header('Content-Type: application/json');
            return json_encode([
                'status'  => $this->status,
                'message' => $this->message,
                'payload' => $this->content,
            ]);
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
