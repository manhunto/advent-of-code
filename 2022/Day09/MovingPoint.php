<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day09;

class MovingPoint
{
    private array $visitedPoints;
    public function __construct(
        public int $x = 0,
        public int $y = 0,
    ) {
        $this->addPositionToVisitedPoints();
    }

    private function addPositionToVisitedPoints(): void
    {
        $key = $this->x . '/' . $this->y;
        $this->visitedPoints[$key] ??= 0;
        ++$this->visitedPoints[$key];
    }

    public function moveInDirection(string $direction): void
    {
        $diffX = 0;
        $diffY = 0;

        switch ($direction) {
            case 'R' :
                $diffX++;
                break;
            case 'L':
                $diffX--;
                break;
            case 'U' :
                $diffY++;
                break;
            case 'D':
                $diffY--;
                break;
        }

        $this->move($diffX, $diffY);
    }

    public function moveTowards(MovingPoint $head): void
    {
        $diffX = $head->x - $this->x;
        $diffY = $head->y - $this->y;

        $distanceY = abs($diffY);
        $distanceX = abs($diffX);

        // left or right
        if ($diffY === 0 && $distanceX > 1) {
            $this->move($diffX > 1 ? 1 : -1, 0);
        }

        //up or down
        if ($diffX === 0 && $distanceY > 1) {
            $this->move(0, $diffY > 1 ? 1 : -1);
        }

        // diagonally
        if ($distanceX + $distanceY > 2) {
            $newDiffX = $this->x < $head->x ? 1 : -1;
            $newDiffY = $this->y < $head->y ? 1 : -1;

            $this->move($newDiffX, $newDiffY);
        }
    }

    public function countVisitedPointAtLeastOnce(): int
    {
        $visitedAtLeastOnce = array_filter($this->visitedPoints, static fn (int $visitCount): bool => $visitCount > 0);

        return count($visitedAtLeastOnce);
    }

    private function move(int $diffX, int $diffY): void
    {
        $this->x += $diffX;
        $this->y += $diffY;

        $this->addPositionToVisitedPoints();
    }
}
