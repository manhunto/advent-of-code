<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Point;

#[SolutionAttribute(
    name: 'Beacon Exclusion Zone',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        /** @var Sensor[] $sensors */
        $sensors = [];
        $lineToCheck = null;

        foreach ($input->asArray() as $row) {
            if (preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $row, $matches)) {
                [, $sx, $sy, $bx, $by] = $matches;

                $sensors[] = new Sensor(new Point((int) $sx, (int) $sy), new Point((int) $bx, (int) $by));
            } elseif(preg_match('/^y=(\d+)$/', $row, $matches)) {
                $lineToCheck = (int) $matches[1];
            }
        }

        $coveredYPositions = [];
        $beaconsInThisLine = [];

        foreach ($sensors as $sensor) {
            $coveredYPositions = [
                ...$coveredYPositions,
                ...$sensor->getCoveredXPositionsInY($lineToCheck),
            ];

            if ($sensor->beaconLocation->y === $lineToCheck) {
                $beaconsInThisLine[] = $sensor->beaconLocation->x;
            }
        }

        $countPositionsCannotContainBeacon = Collection::create($coveredYPositions)
            ->diff($beaconsInThisLine)
            ->unique()
            ->count();
    
        return new Result($countPositionsCannotContainBeacon);
    }
}
