<?php

declare(strict_types=1);

namespace App\Utils;

class Point3D implements \Stringable
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s,%s,%s', $this->x, $this->y, $this->z);
    }

    public function move(int $xBy, int $yBy, int $zBy): self
    {
        return new self(
            $this->x + $xBy,
            $this->y + $yBy,
            $this->z + $zBy
        );
    }

    public function equals(Point3D $pointToCheck): bool
    {
        return $this->x === $pointToCheck->x && $this->y === $pointToCheck->y && $this->z === $pointToCheck->z;
    }

    public function manhattanDistance(Point3D $other): float
    {
        return abs($this->x - $other->x) + abs($this->y - $other->y) + abs($this->z - $other->z);
    }
}
