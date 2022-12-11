<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day11;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Monkey in the Middle',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $monkeys = $this->parseInput($input);

        for ($round = 0; $round < 20; $round++) {
            foreach ($monkeys as $monkey) {
                $monkey->inspectItems($monkeys);
            }
        }

        $inspectedItems = array_map(static fn (Monkey $monkey): int => $monkey->getInspectedItemsCount(), $monkeys);

        sort($inspectedItems);

        $mostActiveMonkeys = array_slice($inspectedItems, -2);
        $monkeyBusiness = $mostActiveMonkeys[0] * $mostActiveMonkeys[1];

        return new Result($monkeyBusiness);
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
        preg_match('/Monkey (\d+):/', $monkeyNoteByLines[0], $matches);
        $name = (int) $matches[1];

        preg_match('/Starting items: ([0-9,\s]+)/', $monkeyNoteByLines[1], $matches);
        $items = explode(', ', $matches[1]);

        preg_match('/Operation: new = old (.*)/', $monkeyNoteByLines[2], $matches);
        $operationInstruction = $matches[1];

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

        if (preg_match('/Test: divisible by (\d+)/', $monkeyNoteByLines[3], $matches)) {
            $divisibleBy = (int) $matches[1];
        } else {
            throw new \LogicException('Unhandled test: ' . $monkeyNoteByLines[3]);
        }

        preg_match('/throw to monkey (\d+)/', $monkeyNoteByLines[4], $matches);
        $ifTrue = (int) $matches[1];
        preg_match('/throw to monkey (\d+)/', $monkeyNoteByLines[5], $matches);
        $ifFalse = (int) $matches[1];

        return new Monkey(
            $name,
            array_map(static fn (int $worryLevel) => new Item($worryLevel), $items),
            $operation,
            $divisibleBy,
            $ifTrue,
            $ifFalse,
        );
    }

    /**
     * @param Monkey[] $monkeys
     */
    private function print(array $monkeys)
    {
        foreach ($monkeys as $number => $monkey) {
            $worryLevels = implode(', ', $monkey->items());
            echo sprintf('Monkey %d: %s', $number, $worryLevels) . PHP_EOL;
        }
    }
}
