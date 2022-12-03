<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day03;

use App\Input;
use App\Result;
use App\Solver;

final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $sum = 0;

        foreach ($input->asArray() as $rucksack) {
            $items = str_split($rucksack);
            $itemsCount = count($items) / 2;
            $secondCompartment = array_splice($items, $itemsCount, $itemsCount);

            $sharedItems = array_unique(array_intersect($items, $secondCompartment));
            $priority = $this->getPriority(reset($sharedItems));
            $sum += $priority;
        }

        return new Result($sum);
    }

    private function getPriority(string $character): int
    {
        $ord = ord($character);

        if ($ord >= 97) {
            return $ord - 96;
        }

        return $ord - 38;
    }
}
