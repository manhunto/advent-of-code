<?php

declare(strict_types=1);

namespace App;

final class PuzzleMetadata
{

    public function __construct(
        private readonly string $puzzleInput,
    ) {
    }

    public function getPuzzleInput(): string
    {
        return $this->puzzleInput;
    }
}
