<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day17;

use App\Utils\Collection;
use App\Utils\Map;

class Shape
{
    private const FIGURES = [
        [
            [0, 0, 1, 1, 1, 1, 0]
        ],
        [
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 1, 0, 0, 0]
        ],
        [
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 0, 1, 0, 0],
            [0, 0, 0, 0, 1, 0, 0],
        ],
        [
            [0, 0, 1, 0, 0, 0, 0],
            [0, 0, 1, 0, 0, 0, 0],
            [0, 0, 1, 0, 0, 0, 0],
            [0, 0, 1, 0, 0, 0, 0],
        ],
        [
            [0, 0, 1, 1, 0, 0, 0],
            [0, 0, 1, 1, 0, 0, 0],
        ]

    ];


    private const CAVE_WIDTH = 7;
    private array $layout;
    private int $maxX;
    private int $minX;

    public function __construct(
        array $layout,
        int $initRow,
    ) {
        $new = [];
        foreach ($layout as $y => $row) {
            $new[$y + $initRow] = $row;
        }

        $this->layout = $new;
        $this->calculateBoundaries();
    }

    public static function createWithShapeNumber(int $shapeNumber, int $initRow): self
    {
        return new self(
            self::FIGURES[$shapeNumber % count(self::FIGURES)]  ?? throw new \LogicException('Unexpected shape ' . $shapeNumber),
            $initRow
        );
    }

    public function moveRight(): void
    {
        if ($this->maxX + 1 >= self::CAVE_WIDTH) {
            return;
        }

        $new = [];
        foreach ($this->layout as $y => $row) {
            foreach (array_reverse($row, true) as $x => $value) {
                if ($value === 1) {
                    $new[$y][$x + 1] = 1;
                }
                $new[$y][$x] = 0;
            }

            $tmp = $new[$y];
            ksort($tmp);
            $new[$y] = $tmp;
        }

        $this->layout = $new;
        $this->calculateBoundaries();
    }

    public function moveLeft(): void
    {
        if ($this->minX <= 0) {
            return;
        }

        $new = [];
        foreach ($this->layout as $y => $row) {
            foreach ($row as $x => $value) {
                if ($value === 1) {
                    $new[$y][$x - 1] = 1;
                }
                $new[$y][$x] = 0;
            }

            $tmp = $new[$y];
            ksort($tmp);
            $new[$y] = $tmp;
        }

        $this->layout = $new;
        $this->calculateBoundaries();
    }

    public function print(): void
    {
        foreach (array_reverse($this->layout, true) as $y => $row) {
            echo sprintf('%s |%s|', $y, implode('', $row)) . PHP_EOL;
        }
    }

    public function asArray(): array
    {
        return $this->layout;
    }

    public function asArrayOnlyWithShape(): array
    {
        return Collection::create($this->layout)
            ->forEach(static fn (array $row) => Collection::create($row)
                ->filter(static fn (int $item) => $item === 1)
                ->toArray()
            )
            ->toArray();
    }

    public function fall(): void
    {
        $new = [];
        foreach ($this->layout as $y => $row) {
            $new[$y - 1] = $row;
        }

        $this->layout = $new;
    }

    public function collide(Map $map): bool
    {
        foreach ($this->layout as $y => $row) {
            foreach ($row as $x => $value) {
                if ($value === 1) {
                    $mapValue = $map->asArray()[$y][$x] ?? '.';
                    if ($mapValue === '#') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getMaxY(): int
    {
        return Collection::create($this->layout)
            ->forEach(static fn (array $row) => Collection::create($row)
                ->filter(static fn (int $item) => $item === 1)
            )
            ->keys()
            ->max();
    }

    private function calculateBoundaries(): void
    {
        $this->maxX = Collection::create($this->layout)
            ->forEach(static fn (array $row) => Collection::create($row)
                ->filter(static fn (int $item) => $item === 1)
                ->keys()
                ->max()
            )->max();

        $this->minX = Collection::create($this->layout)
            ->forEach(static fn (array $row) => Collection::create($row)
                ->filter(static fn (int $item) => $item === 1)
                ->keys()
                ->min()
            )->min();
    }

    public function getHeight(): int
    {
        return count($this->layout);
    }
}
