<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\DirectionalLocation;
use App\Utils\Location;

class EdgeMapping
{
    /** @var array{0: DirectionalLocation, 1: DirectionalLocation} */
    private array $mappings;

    public function add(DirectionalLocation $A, DirectionalLocation $B): void
    {
        $this->mappings[] = [$A, $B->reversedDirection()];
        $this->mappings[] = [$B, $A->reversedDirection()];
    }

    public function getAll(): array
    {
        return $this->mappings;
    }

    public function getFor(Location $point, Direction $direction): DirectionalLocation
    {
        foreach ($this->mappings as $item) {
            /**
             * @var DirectionalLocation $from
             * @var DirectionalLocation $to
             */
            [$from, $to] = $item;

            if ($from->hasTheSame($point, $direction)) {
                return $to;
            }
        }

        throw new \LogicException('There is no mapping form ' . $point . ' in direction ' . $direction->name);
    }
}
