<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day01;

use App\Input;
use App\Result;
use App\Solver;

/**
 * @see https://adventofcode.com/2022/day/1
 */
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $calories = [];
        $caloriesForOneElf = 0;

        foreach ($input->asArray() as $foodItem) {
            if (empty($foodItem)) {
                $calories[] = $caloriesForOneElf;
                $caloriesForOneElf = 0;

                continue;
            }

            $caloriesForOneElf += (int) $foodItem;
        }

        if ($caloriesForOneElf) {
            $calories[] = $caloriesForOneElf;
        }

        // first part
        $max = max($calories);

        // second part
        rsort($calories);
        $topThreeElves = array_slice($calories, 0, 3);
        $sumOfThree = array_sum($topThreeElves);

        return new Result($max, $sumOfThree);
    }
}
