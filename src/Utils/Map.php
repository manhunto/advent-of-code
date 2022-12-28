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

    public static function generateForRowsAndFilledNonExisting(array $mapRow, string $missingElement): self
    {
        $rows = Collection::create($mapRow)
            ->forEach(static fn (array $row) => count($mapRow))
            ->max();
        $columns = count($mapRow);

        $map = self::generateFilled($columns, $rows - 1, $missingElement);

        foreach ($mapRow as $y => $item) {
            foreach ($item as $x => $char) {
                $map->draw($y, $x, $char);
            }
        }

        return $map;
    }

    public function asString(): string
    {
        $grid = $this->grid;
        ksort($grid);

        $string = '';

        foreach ($this->grid as $row) {
            ksort($row);
            $string .= implode('', $row) . PHP_EOL;
        }

        return $string;
    }

    public function drawShape(array $shape, string $string): self
    {
        foreach ($shape as $y => $row) {
            foreach ($row as $x => $value) {
                $this->grid[$y][$x] = $string;
            }
        }

        return $this;
    }

    public function asArray(): array
    {
        return $this->grid;
    }

    public function drawLine(int $startX, int $startY, int $endX, int $endY, string $element): void
    {
        if ($startX !== $endX && $startY !== $endY) {
            throw new \LogicException('Diagonal line is unsupported yet');
        }

        [$x1, $x2] = Arrays::sortedAsc([$startX, $endX]);
        [$y1, $y2] = Arrays::sortedAsc([$startY, $endY]);

        for($y = $y1; $y <= $y2; $y++) {
            for($x = $x1; $x <= $x2; $x++) {
                $this->grid[$y][$x] = $element;
            }
        }
    }

    public function draw(int $y, int $x, string $element): void
    {
        $this->grid[$y][$x] = $element;
    }

    public function hasElement(int $y, int $x, string $element): bool
    {
        $item = $this->grid[$y][$x] ?? null;

        return $item === $element;
    }

    public function hasElementOnPoint(Point $point, string $element): bool
    {
        return $this->hasElement($point->y, $point->x, $element);
    }

    public function cropOnLeft(int $width, string $element): void
    {
        $minX = $this->getMinX();

        for ($x = $minX - 1; $x >= $minX - $width; $x--) {
            foreach ($this->grid as $y => $item) {
                $this->grid[$y][$x] = $element;
            }
        }
    }

    public function cropOnRight(int $width, string $element): void
    {
        $maxX = $this->getMaxX();

        for ($x = $maxX + 1; $x <= $maxX + $width; $x++) {
            foreach ($this->grid as $y => $item) {
                $this->grid[$y][$x] = $element;
            }
        }
    }

    public function cropOnUp(int $height, string $element): void
    {
        $maxX = $this->getMaxX();
        $maxY = $this->getMaxY();

        for ($y = $maxY + 1; $y <= $maxY + $height; $y++) {
            $this->grid[$y] = array_fill(0, $maxX + 1, $element);
        }
    }

    public function cropOnUpToHeight(int $totalHeight, string $element): void
    {
        $maxY = $this->getMaxY();
        $diff = $totalHeight - $maxY;

        if ($diff) {
            $this->cropOnUp($diff, $element);
        }
    }

    public function drawFullWidthLineHorizontally(int $y, string $element): void
    {
        $fromX = $this->getMinX();
        $toX = $this->getMaxX();

        $this->drawLine($fromX, $y, $toX, $y, $element);
    }

    public function printer(): MapPrinter
    {
        return new MapPrinter($this->grid);
    }

    private function getFirstRow(): mixed
    {
        return reset($this->grid);
    }

    private function getMinX(): mixed
    {
        return min($this->getFirstRowPositions());
    }

    private function getMaxX()
    {
        return max($this->getFirstRowPositions());
    }

    private function getFirstRowPositions(): array
    {
        return array_keys($this->getFirstRow());
    }

    private function getMaxY(): mixed
    {
        return max(array_keys($this->grid));
    }

    public function getRow(int $y): array
    {
        return $this->grid[$y] ?? [];
    }

    public function moveRowsTo(int $value): void
    {
        $new = [];
        foreach ($this->grid as $y => $row) {
            $new[$y + $value] = $row;
        }

        $this->grid = $new;
    }

    public function findFirst(string $element): ?Point
    {
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $item) {
                if ($item === $element) {
                    return new Point($x, $y);
                }
            }
        }

        return null;
    }

    public function drawPoint(Point $point, string $element): void
    {
        $this->grid[$point->y][$point->x] = $element;
    }

    public function findFirstInRow(int $y, string $element): ?Point
    {
        foreach ($this->grid[$y] as $x => $item) {
            if ($element === $item) {
                return new Point($x, $y);
            }
        }

        return null;
    }

    public function findLastInRow(int $y, string $element): ?Point
    {
        foreach (array_reverse($this->grid[$y], true) as $x => $item) {
            if ($element === $item) {
                return new Point($x, $y);
            }
        }

        return null;
    }

    public function findFirstInColumn(int $x, string $element): ?Point
    {
        foreach (array_column($this->grid, $x) as $y => $item) {
            if ($element === $item) {
                return new Point($x, $y);
            }
        }

        return null;
    }

    public function findLastInColumn(int $x, string $element): ?Point
    {
        foreach (array_reverse(array_column($this->grid, $x), true) as $y => $item) {
            if ($element === $item) {
                return new Point($x, $y);
            }
        }

        return null;
    }

    public function isInsideMap(int $y, int $x): bool
    {
        return isset($this->grid[$y][$x]);
    }

    public function isPointInsideMap(Point $point): bool
    {
        return $this->isInsideMap($point->y, $point->x);
    }

    public function calculateAreaForElements(array $elements): int
    {
        $area = 0;

        foreach ($this->grid as $row) {
            foreach ($row as $element) {
                if (in_array($element, $elements, true)) {
                    $area++;
                }
            }
        }

        return $area;
    }

    /**
     * @return iterable<Point>
     */
    public function getPointsWithElements(array $elements): iterable
    {
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $element) {
                if (in_array($element, $elements, true)) {
                    yield new Point($x, $y);
                }
            }
        }

    }

    public function getElementForPoint(Point $point): ?string
    {
        return $this->grid[$point->y][$point->x] ?? null;
    }
}
