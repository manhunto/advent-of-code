<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day04;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Camp Cleanup',
    href: 'https://adventofcode.com/2022/day/04'
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $count = 0;

        foreach ($input->asArray() as $row) {
            $pairsRange = explode(',', $row);
            $pairs = array_map(function (string $pair) {
                [$from, $to] = explode('-', $pair);


                return range($from, $to);
            }, $pairsRange);

            $common = array_values(array_intersect(...$pairs));

            if ($common === $pairs[0] || $common === $pairs[1]) {
                ++$count;
            }
        }

        return new Result($count);
    }
}
