<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day07;

class File
{

    public function __construct(
        private readonly string $name,
        private readonly int $size,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
