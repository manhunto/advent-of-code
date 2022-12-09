<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day09;

class Knot
{
    private array $visitedPoints;
    public int $x = 0;
    public int $y = 0;

    public function __construct(
        int $initialX = 0,
        int $initialY = 0,
    ) {
        $this->move($initialX, $initialY);
    }

    public function moveInDirection(string $direction): void
    {
        $diff = match ($direction) {
            'R' => [1, 0],
            'L' => [-1, 0],
            'U' => [0, 1],
            'D' => [0, -1],
        };

        $this->move($diff[0], $diff[1]);
    }

    public function moveTowards(Knot $target): void
    {
        $diffX = $target->x - $this->x;
        $diffY = $target->y - $this->y;

        if (abs($diffY) <= 1 && abs($diffX) <= 1) {
            return; // touching, skip
        }

        $this->move($diffX <=> 0, $diffY <=> 0); // move by one in direction
    }

    public function countPositionsVisitedAtLeastOnce(): int
    {
        return count(array_unique($this->visitedPoints));
    }

    private function move(int $diffX, int $diffY): void
    {
        $this->x += $diffX;
        $this->y += $diffY;

        $this->addPositionToVisitedPoints();
    }

    private function addPositionToVisitedPoints(): void
    {
        $this->visitedPoints[] = $this->x . '/' . $this->y;
    }
}
