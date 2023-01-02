<?php

declare(strict_types=1);

namespace App\Utils;

class Strings implements \Stringable
{
    public static function create(string $string): self
    {
        return new self($string);
    }

    private function __construct(
        private readonly string $string
    ) {
    }

    public function reverse(): self
    {
        return new self(strrev($this->string));
    }

    public function toChars(): Collection
    {
        return Collection::create(str_split($this->string));
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
