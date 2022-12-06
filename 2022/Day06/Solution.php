<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day06;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Tuning Trouble',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $firstPart = $this->getIndexOfFirstStartOfMessageMarker($input, 4);
        $secondPart = $this->getIndexOfFirstStartOfMessageMarker($input, 14);

        return new Result($firstPart, $secondPart);
    }

    private function getIndexOfFirstStartOfMessageMarker(Input $input, int $markerLength): int
    {
        $buffer = $input->asString();
        $bufferAsArray = str_split($buffer);

        foreach ($bufferAsArray as $index => $letter) {
            $slice = array_slice($bufferAsArray, $index, $markerLength);

            if (count(array_unique($slice)) === $markerLength) {
                return $index + $markerLength;
            }
        }

        throw new \LogicException('Cannot find index of first start of message');
    }
}
