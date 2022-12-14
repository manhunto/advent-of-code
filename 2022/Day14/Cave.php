<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day14;

use App\Utils\Map;

class Cave
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
        private readonly Map $map,
    ) {
    }

    public static function generateEmpty(int $minX, int $maxX, int $maxY): self
    {
        return new self(Map::generateFilled($maxY, $maxX, self::AIR, minX: $minX));
    }

    public function print(): void
    {
        echo $this->map->asString();
    }

    public function addRock(int $startX, int $startY, int $endX, int $endY): void
    {
        $this->map->drawLine($startX, $startY, $endX, $endY, self::ROCK);

        $this->updateRockEdgePositions($startX, $endX);
        $this->updateHighestPoint($startY, $endY);
    }

    public function addFloor(): void
    {
        $width = $this->calculateWidthToAddSand(self::SAND_POURING_X);
        $this->map->cropOnRight($width, self::AIR);
        $width = $this->calculateWidthToAddSand(self::SAND_POURING_X);
        $this->map->cropOnLeft($width, self::AIR);

        $this->placeFloor();
    }

    public function pourSand(): void
    {
        while (true) {
            $sand = new Sand(self::SAND_POURING_X, self::SAND_POURING_Y);

            while (true) {
                if ($this->hasSandReachAbyss($sand)) {
                    break 2;
                }

                if (false === $this->move($sand)) {
                    $this->placeSand($sand);

                    if ($this->sandIsBlocked($sand)) {
                        break 2;
                    }

                    break; // next sand
                }
            }
        }
    }

    private function placeSand(Sand $sand): void
    {
        $this->map->draw($sand->y, $sand->x, self::SAND);
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
        return $this->map->hasElement($y, $x, self::AIR);
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

    private function calculateWidthToAddSand(int $x): int
    {
        return $this->highestY - self::SAND_POURING_Y;
    }

    private function placeFloor(): void
    {
        $floorY = $this->highestY + 2;
        $this->map->drawFullWidthLineHorizontally($floorY, self::ROCK);
    }
}
