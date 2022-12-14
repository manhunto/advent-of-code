<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day13;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;

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
        return Collection::explode(PHP_EOL . PHP_EOL, $input->asString())
            ->forEach(static fn(string $string) => Pair::parse($string))
            ->indicesStartAtOne()
            ->getIndices(static fn (Pair $pair) => $pair->isRightOrder())
            ->sum();
    }

    private function solvePartTwo(Input $input): int
    {
        return Collection::create([...$input->asArrayWithoutEmptyLines(), self::FIRST_DIVIDER, self::SECOND_DIVIDER])
            ->forEach(static fn (string $packetAsString) => Packet::parse($packetAsString))
            ->uasort(static fn(Packet $A, Packet $B) => $A->isLowerThan($B) ? -1 : 1)
            ->forEach(static fn(Packet $packet) => $packet->encode())
            ->indicesStartAtOne()
            ->getIndicesForItemsInArray([self::FIRST_DIVIDER, self::SECOND_DIVIDER])
            ->multiply()
        ;
    }
}
