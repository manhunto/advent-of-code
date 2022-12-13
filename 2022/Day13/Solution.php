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
    private const FIRST_DIVIDER = '[[2]]';
    private const SECOND_DIVIDER = '[[6]]';

    public function solve(Input $input): Result
    {
        $partOne = $this->solvePartOne($input);
        $partTwo = $this->solvePartTwo($input);


        return new Result($partOne, $partTwo);
    }

    private function solvePartOne(Input $input): int|float
    {
        $pairsAsString = explode(PHP_EOL . PHP_EOL, $input->asString());
        $pairs = array_map(static fn(string $string) => Pair::parse($string), $pairsAsString);

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

        return array_sum($pairIndicesInRightOrder);
    }

    private function solvePartTwo(Input $input): int
    {
        $packetsAsString = [...array_filter($input->asArray()), self::FIRST_DIVIDER, self::SECOND_DIVIDER];

        $packets = array_map(static fn (string $packetAsString) => Packet::parse($packetAsString), $packetsAsString);

        uasort($packets, static fn(Packet $A, Packet $B) => $A->isLowerThan($B) ? -1 : 1);

        $resultPacketsAsStringWithIndices = array_combine(
            range(1, count($packets)),
            array_values(array_map(static fn (Packet $packet) => $packet->encode(), $packets))
        );

        $indexOfFirstDivider = array_search(self::FIRST_DIVIDER, $resultPacketsAsStringWithIndices, true);
        $indexOfSecondDivider = array_search(self::SECOND_DIVIDER, $resultPacketsAsStringWithIndices, true);

        return $indexOfFirstDivider * $indexOfSecondDivider;
    }
}
