<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day03;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Rucksack Reorganization',
    href: 'https://adventofcode.com/2022/day/3'
)]
final class Solution implements Solver
{
    private const ELVES_IN_GROUP = 3;
    private const SMALL_A_DEC = 97;
    private const SMALl_A_PRIORITY = 1;
    private const BIG_A_DEC = 65;
    private const BIG_A_PRIORITY = 27;

    public function solve(Input $input): Result
    {
        $partOne = $this->partOne($input);
        $partTwo = $this->partTwo($input);

        return new Result($partOne, $partTwo);
    }

    private function partOne(Input $input): int
    {
        $sum = 0;

        foreach ($input->asArray() as $rucksack) {
            $items = str_split($rucksack);
            $itemsCountInCompartment = count($items) / 2;
            $compartments = array_chunk($items, $itemsCountInCompartment);
            $sharedItems = array_unique(array_intersect(...$compartments));
            $sum += $this->getPriority(reset($sharedItems));
        }

        return $sum;
    }

    private function partTwo(Input $input): int
    {
        $sum = 0;
        $chunks = array_chunk($input->asArray(), self::ELVES_IN_GROUP);

        foreach ($chunks as $chunk) {
            $chunkLetters = array_map('str_split', $chunk);
            $badge = array_unique(array_intersect(...$chunkLetters));
            $sum += $this->getPriority(reset($badge));
        }

        return $sum;
    }

    private function getPriority(string $character): int
    {
        $ord = ord($character);

        if ($ord >= self::SMALL_A_DEC) {
            return $ord - self::SMALL_A_DEC + self::SMALl_A_PRIORITY;
        }

        return $ord - self::BIG_A_DEC + self::BIG_A_PRIORITY;
    }
}
