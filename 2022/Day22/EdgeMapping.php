<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\Point;
use SebastianBergmann\Diff\Diff;

class EdgeMapping
{
    /** @var array{0: DirectionFromPoint, 1: DirectionFromPoint} */
    private array $mappings;

    public function add(DirectionFromPoint $A, DirectionFromPoint $B): void
    {
        $this->mappings[] = [$A, $B->reversed()];
        $this->mappings[] = [$B, $A->reversed()];
    }

    public function getAll(): array
    {
        return $this->mappings;
    }

    public function getFor(Point $point, Direction $direction): DirectionFromPoint
    {
        foreach ($this->mappings as $item) {
            /**
             * @var DirectionFromPoint $from
             * @var DirectionFromPoint $to
             */
            [$from, $to] = $item;

            if ($from->hasTheSame($point, $direction)) {
                return $to;
            }
        }

        throw new \LogicException('There is no mapping form ' . $point . ' in direction' . $direction->name);
    }
}
