<?php

namespace OpenServer\Traits;

trait Makeable
{
    /**
     * @param mixed ...$args
     * @return static
     * @static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }
}
