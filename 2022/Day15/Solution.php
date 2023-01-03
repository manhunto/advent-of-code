<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Location;
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

                $sensors[] = new Sensor(new Location((int) $sx, (int) $sy), new Location((int) $bx, (int) $by));
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
        $rc = $this->getSensorRangesInLine($sensors, $lineToCheck);
        $rcBeacons = $this->getBeaconsInLineAsRanges($sensors, $lineToCheck);

        foreach ($rcBeacons as $range) {
            $rc->diff($range);
        }

        return $rc->length();
    }

    /**
     * @param Sensor[] $sensors
     */
    private function solveSecondPart(array $sensors, int $max): int
    {
        $searchRange = new Range(0, $max);
        foreach ($sensors as $sensor) {
            foreach ($sensor->getPointsOnBorderPlusOne($searchRange) as $location) {
                if ($this->isNotInRangeOfAnySensor($sensors, $location)) {
                    return $this->calculateTuningSignal($location->x, $location->y);
                }
            }
        }

        throw new \LogicException('Something went wrong');
    }

    /**
     * @param Sensor[] $sensors
     */
    private function getSensorRangesInLine(array $sensors, int $line): RangeCollection
    {
        $sensorRanges = Collection::create($sensors)
            ->forEach(static fn(Sensor $sensor) => $sensor->getRangeOnLine($line))
            ->filter()
            ->toArray();

        $rc = new RangeCollection();
        $rc->union(...$sensorRanges);

        return $rc;
    }

    /**
     * @param Sensor[] $sensors
     * @return Range[]
     */
    private function getBeaconsInLineAsRanges(array $sensors, int $lineToCheck): array
    {
        return Collection::create($sensors)
            ->filter(static fn (Sensor $sensor) => $sensor->beaconLocation->y === $lineToCheck)
            ->forEach(static fn (Sensor $sensor) => Range::createForPoint($sensor->beaconLocation->x))
            ->unique()
            ->toArray();
    }

    private function calculateTuningSignal(int $x, int $y): int
    {
        return $x * 4_000_000 + $y;
    }

    /**
     * @param Sensor[] $sensors
     */
    private function isNotInRangeOfAnySensor(array $sensors, Location $location): bool
    {
        foreach ($sensors as $sensor) {
            if ($sensor->canBeReachedBySensor($location)) {
                return false;
            }
        }

        return true;
    }
}
