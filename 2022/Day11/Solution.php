<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day11;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Regex;

#[SolutionAttribute(
    name: 'Monkey in the Middle',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $part1 = $this->solvePart1($input);
        $part2 = $this->solvePart2($input);

        return new Result($part1, $part2);
    }

    private function solvePart1(Input $input): int
    {
        $monkeys = $this->parseInput($input);
        $reduceWorryLevelFunc = static fn (Item $item) => $item->divideByThree();

        for ($round = 0; $round < 20; $round++) {
            foreach ($monkeys as $monkey) {
                $monkey->inspectItems($monkeys, $reduceWorryLevelFunc);
            }
        }

        return $this->calculateMonkeyBusiness($monkeys);
    }

    private function solvePart2(Input $input): int
    {
        $monkeys = $this->parseInput($input);
        $greatestCommonDivisor = $this->getGreatestCommonDivisor($monkeys);
        $reduceWorryLevelFunc = static fn (Item $item) => $item->mod($greatestCommonDivisor);

        for ($round = 0; $round < 10_000; $round++) {
            foreach ($monkeys as $monkey) {
                $monkey->inspectItems($monkeys, $reduceWorryLevelFunc);
            }
        }

        return $this->calculateMonkeyBusiness($monkeys);
    }

    /**
     * @return Monkey[]
     */
    private function parseInput(Input $input): array
    {
        $monkeyNotes = explode(PHP_EOL . PHP_EOL, $input->asString());

        return array_map(
            fn (string $monkeyNote): Monkey => $this->parseNotesForMonkey($monkeyNote),
            $monkeyNotes
        );
    }

    private function parseNotesForMonkey(string $notesForMonkey): Monkey
    {
        $monkeyNoteByLines = explode(PHP_EOL, $notesForMonkey);

        $items = explode(', ', Regex::matchSingle('/Starting items: ([0-9,\s]+)/', $monkeyNoteByLines[1]));
        $operationInstruction = Regex::matchSingle('/Operation: new = old (.*)/', $monkeyNoteByLines[2]);

        [$op, $value] = explode(' ', $operationInstruction);

        if ($op === '+') {
            $operation = static fn(Item $item) => $item->plus((int)$value);
        } elseif ($op === '*') {
            if ($value === 'old') {
                $operation = static fn(Item $item) => $item->pow();
            } else {
                $operation = static fn(Item $item) => $item->multiply((int)$value);
            }
        }
        $divisibleBy = (int) Regex::matchSingle('/Test: divisible by (\d+)/', $monkeyNoteByLines[3]);
        $ifTrue = (int) Regex::matchSingle('/throw to monkey (\d+)/', $monkeyNoteByLines[4]);
        $ifFalse = (int) Regex::matchSingle('/throw to monkey (\d+)/', $monkeyNoteByLines[5]);

        return new Monkey(
            array_map(static fn (int $worryLevel) => new Item($worryLevel), $items),
            $operation ?? throw new LogicException('Cannot parse operation: '. $operationInstruction),
            $divisibleBy,
            $ifTrue,
            $ifFalse,
        );
    }

    /**
     * Modulo with the greatest common divisor of test numbers don't break calculation on worry level.
     */
    private function getGreatestCommonDivisor(array $monkeys): int
    {
        return array_reduce($monkeys, static fn(int $value, Monkey $monkey) => $value * $monkey->divisible, 1);
    }

    private function calculateMonkeyBusiness(array $monkeys): int
    {
        $inspectedItems = array_map(static fn(Monkey $monkey): int => $monkey->getInspectedItemsCount(), $monkeys);
        rsort($inspectedItems);

        return $inspectedItems[0] * $inspectedItems[1];
    }
}
