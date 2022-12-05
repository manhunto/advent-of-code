<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day05;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Supply Stacks',
    href: 'https://adventofcode.com/2022/day/05'
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        [$plan, $instructions] = explode(PHP_EOL . PHP_EOL, $input->asString());
        $crates = $this->getCrates($plan);

        foreach (explode(PHP_EOL, $instructions) as $command) {
            preg_match('/move (.*) from (.*) to (.*)/', $command, $matches);

            [, $quantity, $from, $to] = $matches;

            $stackFrom = $crates[$from - 1];
            $cratesToMove = $stackFrom->unshift((int) $quantity);
            $stackTo = $crates[$to - 1];
            $stackTo->add($cratesToMove);
        }

        $topCrates = array_map(static fn (Stack $stack): string => $stack->getTopCrate(), $crates);
        $topCratesInRow = implode('', $topCrates);

        return new Result($topCratesInRow);
    }

    /**
     * @return Stack[]
     */
    private function getCrates(string $plan): array
    {
        $crates = [];

        foreach (explode(PHP_EOL, $plan) as $row) {
            foreach (str_split($row) as $key => $item) {
                if (in_array($item, range('A', 'Z'), true)) {
                    $column = ($key - 1) / 4;
                    $crates[$column][] = $item;
                }
            }
        }

        ksort($crates);

        return array_map(static fn (array $column) => new Stack($column), $crates);
    }
}
