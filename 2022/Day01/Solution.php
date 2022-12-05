<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day01;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Calorie Counting'
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $foodItems = explode(PHP_EOL . PHP_EOL, $input->asString());

        $calories = array_map(static fn (string $elfFood): int
            => array_sum(explode(PHP_EOL, $elfFood)),
            $foodItems);

        // first part
        $max = max($calories);

        // second part
        sort($calories);
        $topThreeElves = array_slice($calories, -3);
        $sumOfThree = array_sum($topThreeElves);

        return new Result($max, $sumOfThree);
    }
}
