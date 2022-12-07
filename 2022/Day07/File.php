<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day07;

final class File
{
    public function __construct(
        public readonly string $name,
        public readonly int $size,
    ) {
    }
}
