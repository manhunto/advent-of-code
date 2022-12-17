<?php

declare(strict_types=1);

namespace App\Utils\PathFinding;

class Node
{
    /**
     * @param string[] $neighbours
     */
    public function __construct(
        public readonly string $name,
        public readonly array $neighbours,
    ) {
    }
}
