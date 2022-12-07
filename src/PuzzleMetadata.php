<?php

declare(strict_types=1);

namespace App;

final class PuzzleMetadata
{

    public function __construct(
        public readonly string $puzzleInput,
        public readonly string $puzzleName,
    ) {
    }
}
