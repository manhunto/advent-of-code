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
        $max = null;

        foreach ($input->asArray() as $row) {
            if (preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $row, $matches)) {
                [, $sx, $sy, $bx, $by] = $matches;

                $sensors[] = new Sensor(new Point((int) $sx, (int) $sy), new Point((int) $bx, (int) $by));
            } elseif(preg_match('/^y=(\d+)$/', $row, $matches)) {
                $lineToCheck = (int) $matches[1];
            } elseif (preg_match('/^max=(\d+)$/', $row, $matches)) {
                $max = (int) $matches[1];
            }
        }

        $countPositionsCannotContainBeacon = $this->solveFirstPart($sensors, $lineToCheck);
        $tuningSignal = $this->solveSecondPart($sensors, $max);


        return new Result($countPositionsCannotContainBeacon, $tuningSignal);
    }

    /**
     * @param Sensor[] $sensors
     */
    private function solveFirstPart(array $sensors, int $lineToCheck): int
    {
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

        return Collection::create($coveredYPositions)
            ->diff($beaconsInThisLine)
            ->unique()
            ->count();
    }

    /**
     * @param Sensor[] $sensors
     */
    private function solveSecondPart(array $sensors, int $max): int
    {
        $allLines = range(0, $max);

        foreach ($allLines as $line) {
            $coveredYPositions = [];

            foreach ($sensors as $sensor) {
                $coveredYPositions = [
                    ...$coveredYPositions,
                    ...$sensor->getCoveredXPositionsInY($line),
                ];
            }

            $diff = array_values(array_diff($allLines, $coveredYPositions));

            if (!empty($diff)) {
                return $this->calculateTuningSignal($diff[0], $line);
            }
        }
    }

    private function calculateTuningSignal(int $x, int $y): int
    {
        return $x * 4_000_000 + $y;
    }
}
