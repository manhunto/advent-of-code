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

    public function moveTowards(Knot $head): void
    {
        $diffX = $head->x - $this->x;
        $diffY = $head->y - $this->y;

        $distance = (abs(($diffX ** 2) + ($diffY ** 2))) ** 0.5;

        $moveXByOne = $diffX > 0 ? 1 : -1;
        $moveYByOne = $diffY > 0 ? 1 : -1;

        // diagonally, all directions by one towards
        if ($distance > 2) {
            $this->move($moveXByOne, $moveYByOne);
        }

        // +1 in direction - up, down, left, right
        if ($distance > 1) {
            $this->move(
                $diffY !== 0 ? 0 : $moveXByOne,
                $diffX !== 0 ? 0 : $moveYByOne
            );
        }
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
