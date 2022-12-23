<?php

declare(strict_types=1);

namespace App\Utils;

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

    public function moveRight(): self
    {
        return new self($this->x + 1, $this->y);
    }

    public function moveDown(): self
    {
        return new self($this->x, $this->y + 1);
    }

    public function moveLeft(): self
    {
        return new self($this->x - 1, $this->y);
    }

    public function moveUp(): self
    {
        return new self($this->x, $this->y - 1);
    }

    public function isBeforeInColumn(self $than): bool
    {
        $this->validateTheSameColumn($than);

        return $this->y < $than->y;
    }

    public function isAfterInColumn(self $than): bool
    {
        $this->validateTheSameColumn($than);

        return $this->y > $than->y;
    }

    public function isBeforeInRow(self $than): bool
    {
        $this->validateTheSameRow($than);

        return $this->x < $than->x;
    }

    public function isAfterInRow(self $than): bool
    {
        $this->validateTheSameRow($than);

        return $this->x > $than->x;
    }

    private function validateTheSameRow(self $than): void
    {
        if ($this->isTheSameRow($than) === false) {
            throw new \LogicException('Point are not in the same row');
        }
    }

    private function isTheSameRow(self $than): bool
    {
        return $this->y === $than->y;
    }

    private function validateTheSameColumn(self $than): void
    {
        if ($this->isTheSameColumn($than) === false) {
            throw new \LogicException('Point are not in the same column');
        }
    }

    private function isTheSameColumn(self $than): bool
    {
        return $this->x === $than->x;
    }
}
