<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day13;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Distress Signal',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $pairsAsString = explode(PHP_EOL.PHP_EOL, $input->asString());
        $pairs = array_map(static fn (string $string) => Pair::parse($string), $pairsAsString);

        /** @var Pair[] $pairs */
        $pairs = array_combine(
            range(1, count($pairs)),
            array_values($pairs)
        );

        $pairIndicesInRightOrder = [];

        foreach ($pairs as $index => $pair) {
            if ($pair->isRightOrder()) {
                $pairIndicesInRightOrder[] = $index;
            }
        }
    
        return new Result(array_sum($pairIndicesInRightOrder));
    }
}
