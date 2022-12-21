<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day20;

class Number
{
    public readonly int $value;
    private string $id;

    public function __construct(int $value)
    {
        $this->value = $value;
        $this->id = uniqid();
    }

    public function isTheSame(Number $other): bool
    {
        return $this->id === $other->id;
    }
}
