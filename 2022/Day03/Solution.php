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
        $partOne = $this->partOne($input);
        $partTwo = $this->partTwo($input);

        return new Result($partOne, $partTwo);
    }

    private function partOne(Input $input): int
    {
        $sum = 0;

        foreach ($input->asArray() as $rucksack) {
            $items = str_split($rucksack);
            $itemsCount = count($items) / 2;
            $secondCompartment = array_splice($items, $itemsCount, $itemsCount);

            $sharedItems = array_unique(array_intersect($items, $secondCompartment));
            $sum += $this->getPriority(reset($sharedItems));
        }

        return $sum;
    }

    private function partTwo(Input $input): int
    {
        $sum = 0;
        $chunks = array_chunk($input->asArray(), 3);

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

        if ($ord >= 97) {
            return $ord - 96;
        }

        return $ord - 38;
    }
}
