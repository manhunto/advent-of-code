<?php

declare(strict_types=1);

namespace App\Utils;

use App\Utils\Output\Console;

class Map
{
    public function __construct(
        private array $grid,
    ) {
    }

    public static function generateFilled(int $maxY, int $maxX, string $element, int $minX = 0, int $minY = 0): self
    {
        $grid = [];

        for ($y = $minY; $y < $maxY + 1; $y++) {
            for ($x = $minX; $x < $maxX + 1; $x++) {
                $grid[$y][$x] = $element;
            }
        }

        return new self($grid);
    }

    public static function generateForRowsAndFilledNonExisting(array $mapRow, string $missingElement): self
    {
        $rows = Collection::create($mapRow)
            ->forEach(static fn (array $row) => max(array_keys($row)))
            ->max();
        $columns = count($mapRow);

        $map = self::generateFilled($columns, $rows, $missingElement);

        foreach ($mapRow as $y => $item) {
            foreach ($item as $x => $char) {
                $map->draw($y, $x, $char);
            }
        }

        return $map;
    }

    /**
     * @param Location[] $locations
     */
    public static function generateForLocations(array $locations, string $locationElement, string $emptyElement): self
    {
        $tmpLocations = $locations;
        $firstPoint = array_shift($tmpLocations);

        $xRange = Range::createForPoint($firstPoint->x);
        $yRange = Range::createForPoint($firstPoint->y);

        foreach ($tmpLocations as $location) {
            $xRange = $xRange->expandTo($location->x);
            $yRange = $yRange->expandTo($location->y);
        }

        $map = self::generateFilledForRanges($xRange, $yRange, $emptyElement);

        foreach ($locations as $location) {
            $map->drawPoint($location, $locationElement);
        }

        return $map;
    }

    public static function generateFilledForRanges(Range $xRange, Range $yRange, string $element): self
    {
        return self::generateFilled($yRange->to, $xRange->to, $element, $xRange->from, $yRange->from);
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

    public function hasElementOnPoint(Location $point, string $element): bool
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

    public function findFirst(string $element): ?Location
    {
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $item) {
                if ($item === $element) {
                    return new Location($x, $y);
                }
            }
        }

        return null;
    }

    public function findLast(string $element): ?Location
    {
        foreach (array_reverse($this->grid, true) as $y => $row) {
            foreach (array_reverse($row, true) as $x => $item) {
                if ($item === $element) {
                    return new Location($x, $y);
                }
            }
        }

        return null;
    }

    public function drawPoint(Location $point, string $element): void
    {
        $this->grid[$point->y][$point->x] = $element;
    }

    public function findFirstInRow(int $y, string $element): ?Location
    {
        foreach ($this->grid[$y] as $x => $item) {
            if ($element === $item) {
                return new Location($x, $y);
            }
        }

        return null;
    }

    public function findLastInRow(int $y, string $element): ?Location
    {
        foreach (array_reverse($this->grid[$y], true) as $x => $item) {
            if ($element === $item) {
                return new Location($x, $y);
            }
        }

        return null;
    }

    public function findFirstInColumn(int $x, string $element): ?Location
    {
        foreach (array_column($this->grid, $x) as $y => $item) {
            if ($element === $item) {
                return new Location($x, $y);
            }
        }

        return null;
    }

    public function findLastInColumn(int $x, string $element): ?Location
    {
        foreach (array_reverse(array_column($this->grid, $x), true) as $y => $item) {
            if ($element === $item) {
                return new Location($x, $y);
            }
        }

        return null;
    }

    public function isInsideMap(int $y, int $x): bool
    {
        return isset($this->grid[$y][$x]);
    }

    public function isPointInsideMap(Location $point): bool
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
     * @return iterable<Location>
     */
    public function getPointsWithElements(array $elements): iterable
    {
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $element) {
                if (in_array($element, $elements, true)) {
                    yield new Location($x, $y);
                }
            }
        }

    }

    public function getElementForLocation(Location $point): ?string
    {
        return $this->grid[$point->y][$point->x] ?? null;
    }
}
