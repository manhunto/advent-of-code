<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day05;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Supply Stacks',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        [$plan, $instructions] = explode(PHP_EOL . PHP_EOL, $input->asString());

        $firstStacks = $this->getStacks($plan);
        $secondStacks = $this->getStacks($plan);

        foreach (explode(PHP_EOL, $instructions) as $instruction) {
            $command = Command::fromString($instruction);

            $cratesToMove = $firstStacks[$command->fromStack]->takeFromTopOneByOne($command->quantity);
            $firstStacks[$command->toStack]->add($cratesToMove);

            $cratesToMove = $secondStacks[$command->fromStack]->takeFromTopInOneMove($command->quantity);
            $secondStacks[$command->toStack]->add($cratesToMove);
        }

        $firstTopCratesInRow = $this->getTopCratesAsString($firstStacks);
        $secondTopCratesInRow = $this->getTopCratesAsString($secondStacks);

        return new Result($firstTopCratesInRow, $secondTopCratesInRow);
    }

    /**
     * @return Stack[]
     */
    private function getStacks(string $plan): array
    {
        $characters = range('A', 'Z');
        $crates = [];

        foreach (explode(PHP_EOL, $plan) as $row) {
            foreach (str_split($row) as $key => $item) {
                if (in_array($item, $characters, true)) {
                    $column = ceil($key / 4);
                    $crates[$column][] = $item;
                }
            }
        }

        ksort($crates);

        return array_map(static fn (array $column) => new Stack($column), $crates);
    }

    private function getTopCratesAsString(array $crates): string
    {
        $topCrates = array_map(static fn(Stack $stack): string => $stack->getTopCrate(), $crates);

        return implode('', $topCrates);
    }
}
