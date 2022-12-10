<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day10;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Cathode-Ray Tube',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $cpu = new CPU();
        foreach ($input->asArray() as $row) {
            if ($row === 'noop') {
                $cpu->noop();
            } elseif (str_starts_with($row, 'addx')) {
                [, $value] = explode(' ', $row);

                $cpu->addx((int) $value);
            }
        }

        $signalStrengths = $cpu->getSignalStrengthAtCycles(20, 60, 100, 140, 180, 220);
        $rows = $cpu->getPixelsInRowsOnCRT(40);

        $this->drawCRTScreen($rows);

        return new Result(array_sum($signalStrengths));
    }

    private function drawCRTScreen(array $rows): void
    {
        foreach ($rows as $row) {
            echo implode('', $row) . PHP_EOL;
        }

        echo PHP_EOL;
    }
}
