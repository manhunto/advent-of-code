<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day14;

class Grid
{
    private const AIR = '.';
    private const ROCK = '#';
    private const SAND = 'o';

    private const SAND_POURING_X = 500;
    private const SAND_POURING_Y = 0;

    private int $highestY = PHP_INT_MIN;
    private int $rockMinX = PHP_INT_MAX;
    private int $rockMaxX = PHP_INT_MIN;
    private int $sandInCave = 0;

    public function __construct(
        private array $grid,
    ) {
    }

    public static function generateEmpty(int $minX, int $maxX, int $maxY): self
    {
        return new self(
            array_fill(
                0,
                $maxY,
                array_fill($minX, $maxX - $minX + 1, self::AIR)
            )
        );
    }

    public function print(): void
    {
        $grid = $this->grid;
        ksort($grid);

        foreach ($this->grid as $row) {
            ksort($row);
            foreach ($row as $item) {
                echo $item;
            }
            echo PHP_EOL;
        }
    }

    public function addRock(int $startX, int $startY, int $endX, int $endY): void
    {
        if ($startX === $endX) { // horizontal
            for($y = min($startY, $endY); $y <= max($startY, $endY); $y++) {
                $this->grid[$y][$startX] = self::ROCK;
                $this->updateHighestPoint($startY, $endY);
            }
        } elseif ($startY === $endY) { // vertical
            for($x = min($startX, $endX); $x <= max($startX, $endX); $x++) {
                $this->grid[$startY][$x] = self::ROCK;
                $this->updateRockEdgePositions($startX, $endX);
            }
        }
    }

    public function addFloor(): void
    {
        $this->widenCaveOnLeftWithAirToFitSand();
        $this->widenCaveOnRightWithAirToFitSand();
        $this->placeFloor();
    }

    public function pourSand(): bool
    {
        $sand = new Sand(self::SAND_POURING_X, self::SAND_POURING_Y);

        while (true) {
            if ($this->hasSandReachAbyss($sand)) {
                return true;
            }

            if (false === $this->move($sand)) {
                $this->placeSand($sand);

                if ($this->sandIsBlocked($sand)) {
                    return true;
                }

                return false;
            }
        }
    }

    private function placeSand(Sand $sand): void
    {
        $this->grid[$sand->y][$sand->x] = self::SAND;
        $this->sandInCave++;
    }

    private function move(Sand $sand): bool
    {
        if ($this->canSandMoveHere($sand->x, $sand->y + 1)) {
            $sand->down();
        } else if ($this->canSandMoveHere($sand->x - 1, $sand->y + 1)) {
            $sand->leftDown();
        } else if ($this->canSandMoveHere($sand->x + 1, $sand->y + 1)) {
            $sand->rightDown();
        } else {
            return false;
        }

        return true;
    }

    private function canSandMoveHere(int $x, int $y): bool
    {
        return $this->grid[$y][$x] === self::AIR;
    }

    private function hasSandReachAbyss(Sand $sand): bool
    {
        return $this->highestY + 1 < $sand->y;
    }

    private function updateHighestPoint(int $startY, int $endY): void
    {
        $this->highestY = max($startY, $endY, $this->highestY);
    }

    private function sandIsBlocked(Sand $sand): bool
    {
        return $sand->x === self::SAND_POURING_X && $sand->y === self::SAND_POURING_Y;
    }

    private function updateRockEdgePositions(int $startX, int $endX): void
    {
        $this->rockMinX = min($startX, $endX, $this->rockMinX);
        $this->rockMaxX = max($startX, $endX, $this->rockMaxX);
    }

    public function countSandInCave(): int
    {
        return $this->sandInCave;
    }

    private function widenCaveOnLeftWithAirToFitSand(): void
    {
        $width = $this->calculateWidthToAddSand($this->rockMinX);

        for ($x = $this->rockMinX - 1; $x >= $this->rockMinX - $width; $x--) {
            foreach ($this->grid as $y => $item) {
                $this->grid[$y][$x] = self::AIR;
            }
        }
    }

    private function widenCaveOnRightWithAirToFitSand(): void
    {
        $width = $this->calculateWidthToAddSand($this->rockMaxX);

        for ($x = $this->rockMaxX + 1; $x <= $this->rockMaxX + $width; $x++) {
            foreach ($this->grid as $y => $item) {
                $this->grid[$y][$x] = self::AIR;
            }
        }
    }

    private function calculateWidthToAddSand(int $x): int
    {
        $height = $this->highestY - self::SAND_POURING_Y + 2;

        return $x - self::SAND_POURING_X + $height;
    }

    private function placeFloor(): void
    {
        $width = count($this->grid[0]);
        $floorY = $this->highestY + 2;

        $this->grid[$floorY] = array_fill(
            min(array_keys($this->grid[$floorY])),
            $width,
            self::ROCK
        );
    }
}
