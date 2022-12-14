<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day14;

class Grid
{
    private const AIR = '.';
    private const ROCK = '#';
    private const SAND = 'o';

    private int $abyssY = PHP_INT_MIN;

    public function __construct(
        private array $grid,
    ) {
    }

    public static function generateEmpty(int $minX, int $maxX, $maxY): self
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
        foreach ($this->grid as $row) {
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
                $this->updateAbyss(max($startY, $endY));
            }
        } elseif ($startY === $endY) {
            for($x = min($startX, $endX); $x <= max($startX, $endX); $x++) {
                $this->grid[$startY][$x] = self::ROCK;
            }
        } else {
            throw new \LogicException('Cannot draw diagonal');
        }
    }

    public function addSand(): bool
    {
        $sand = new Sand(500, 0);

        while (true) {
            if ($this->hasSandReachAbyss($sand)) {
                $this->placeSand($sand);
                return true;
            }

            if (false === $this->move($sand)) {
                $this->placeSand($sand);

                return false;
            }
        }
    }

    private function placeSand(Sand $sand): void
    {
        $this->grid[$sand->y][$sand->x] = self::SAND;
    }

    private function move(Sand $sand): bool
    {
        if ($this->canSandMoveHere($sand->x, $sand->y + 1)) {
            $sand->down();

            return true;
        }

        if ($this->canSandMoveHere($sand->x - 1, $sand->y + 1)) {
            $sand->leftDown();

            return true;
        }

        if ($this->canSandMoveHere($sand->x + 1, $sand->y + 1)) {
            $sand->rightDown();

            return true;
        }

        return false;
    }

    private function canSandMoveHere(int $x, int $y): bool
    {
        return $this->grid[$y][$x] === self::AIR;
    }

    private function hasSandReachAbyss(Sand $sand): bool
    {
        return $this->abyssY < $sand->y;
    }

    private function updateAbyss(int $maxY): void
    {
        $this->abyssY = max($this->abyssY, $maxY);
    }
}
