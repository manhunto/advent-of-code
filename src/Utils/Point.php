<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * @todo move to Utils, maybe convert Sand to this class
 */
class Point
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }

    public function distanceInManhattanGeometry(self $other): int
    {
        return abs($this->x - $other->x) + abs($this->y - $other->y);
    }

    public function distanceInEuclideanGeometry(self $other): float
    {
        return sqrt(abs($this->x - $other->x) ** 2 + abs($this->y - $other->y) ** 2);
    }

    public function equals(self $other): bool
    {
        return $this->x === $other->x && $this->y && $other->y;
    }
}
