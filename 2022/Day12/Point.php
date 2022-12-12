<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day12;

class Point implements \Stringable
{
    private const START_POSITION = 'S';
    private const END_POSITION = 'E';

    public function __construct(
        private readonly string $value,
        public readonly int $y,
        public readonly int $x,
    ) {
    }

    public function getElevation(): int
    {
        if ($this->value === self::START_POSITION) {
            return ord('a');
        }

        if ($this->value === self::END_POSITION) {
            return ord('z');
        }

        return ord($this->value);
    }

    public function canMove(Point $point): bool
    {
        $distance = $this->getElevation() - $point->getElevation();

        return $distance <= 1;
    }

    /**
     * @param array<Point[]> $grid
     * @return Point[]
     */
    public function getNeighboursToMove(array $grid): array
    {
        $neighbours[] = $grid[$this->y - 1][$this->x] ?? null;
        $neighbours[] = $grid[$this->y + 1][$this->x] ?? null;
        $neighbours[] = $grid[$this->y][$this->x - 1] ?? null;
        $neighbours[] = $grid[$this->y][$this->x + 1] ?? null;

        $neighbours = array_filter($neighbours);

        $neighboursToMove = [];

        /** @var Point $neighbour */
        foreach ($neighbours as $neighbour) {
            if ($this->canMove($neighbour)) {
                $neighboursToMove[] = $neighbour;
            }
        }

        return $neighboursToMove;
    }

    public function isStart(): bool
    {
        return $this->value === self::START_POSITION;
    }

    public function isEnd(): bool
    {
        return $this->value === self::END_POSITION;
    }

    public function isTheLowestElevation(): bool
    {
        return $this->value === 'a';
    }

    public function __toString(): string
    {
        return sprintf('%s,%s', $this->y, $this->x);
    }
}
