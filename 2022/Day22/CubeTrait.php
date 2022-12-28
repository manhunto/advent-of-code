<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Point;

trait CubeTrait
{
    private function calculateLengthOfEdge(): int
    {
        $area = $this->map->calculateAreaForElements($this->mapElements);

        return (int) sqrt($area / 6);
    }
}
