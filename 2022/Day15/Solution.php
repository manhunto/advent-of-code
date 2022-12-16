<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Point;
use App\Utils\Range;
use App\Utils\RangeCollection;

#[SolutionAttribute(
    name: 'Beacon Exclusion Zone',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        /** @var Sensor[] $sensors */
        $sensors = [];
        $lineToCheck = $input->inputType->isExample() ? 10 : 2000000;
        $maxCord = $input->inputType->isExample() ? 20 : 4000000;

        foreach ($input->asArray() as $row) {
            if (preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $row, $matches)) {
                [, $sx, $sy, $bx, $by] = $matches;

                $sensors[] = new Sensor(new Point((int) $sx, (int) $sy), new Point((int) $bx, (int) $by));
            }
        }

        $countPositionsCannotContainBeacon = $this->solveFirstPart($sensors, $lineToCheck);
        $tuningSignal = $this->solveSecondPart($sensors, $maxCord);


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
        $searchRange = new Range(0, $max);

        foreach ($searchRange->getItems() as $line) {
            $sensorRanges = array_map(static fn (Sensor $sensor) => $sensor->getRangeOnLine($line), $sensors);
            $sensorRanges = array_filter($sensorRanges);

            $rc = new RangeCollection();
            $rc->union(...$sensorRanges);
            $rc->intersect($searchRange);
            $gaps = $rc->getGaps();

            if (empty($gaps) === false) {
                $gap = $gaps[0];

                if ($gap->hasOneItem() === false) {
                    throw new \LogicException('Something went wrong');
                }

                return $this->calculateTuningSignal($gap->from, $line);
            }
        }
    }

    private function calculateTuningSignal(int $x, int $y): int
    {
        return $x * 4_000_000 + $y;
    }
}
