<?php

declare(strict_types=1);

namespace App\Utils;

class Map
{
    public function __construct(
        private array $grid,
    ) {
    }

    public static function generateFilled(int $maxY, int $maxX, string $element, int $minX = 0, int $minY = 0): self
    {
        return new self(array_fill(
            $minY,
            $maxY,
            array_fill($minX, $maxX - $minX + 1, $element)
        ));
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

    public function drawLine(int $startX, int $startY, int $endX, int $endY, string $element): void
    {
        if ($startX === $endX) { // horizontal
            for($y = min($startY, $endY); $y <= max($startY, $endY); $y++) {
                $this->grid[$y][$startX] = $element;
            }
        } elseif ($startY === $endY) { // vertical
            for($x = min($startX, $endX); $x <= max($startX, $endX); $x++) {
                $this->grid[$startY][$x] = $element;
            }
        } else {
            throw new \LogicException('Diagonal line is unsupported yet');
        }
    }

    public function draw(int $y, int $x, string $element): void
    {
        $this->grid[$y][$x] = $element;
    }

    public function hasElement(int $y, int $x, string $element): bool
    {
        return $this->grid[$y][$x] === $element;
    }

    public function cropOnLeft(int $width, string $element): void
    {
        $firstRow = $this->getFirstRow();
        $minX = min(array_keys($firstRow));

        for ($x = $minX - 1; $x >= $minX - $width; $x--) {
            foreach ($this->grid as $y => $item) {
                $this->grid[$y][$x] = $element;
            }
        }
    }

    public function cropOnRight(int $width, string $element): void
    {
        $firstRow = $this->getFirstRow();
        $maxX = max(array_keys($firstRow));

        for ($x = $maxX + 1; $x <= $maxX + $width; $x++) {
            foreach ($this->grid as $y => $item) {
                $this->grid[$y][$x] = $element;
            }
        }
    }

    public function drawFullWidthLineHorizontally(int $y, string $element): void
    {
        $width = $this->getWidth();
        $this->grid[$y] = array_fill(
            min(array_keys($this->grid[$y])),
            $width,
            $element
        );
    }

    private function getWidth(): int
    {
        return count($this->getFirstRow());
    }

    private function getFirstRow(): mixed
    {
        return reset($this->grid);
    }
}
