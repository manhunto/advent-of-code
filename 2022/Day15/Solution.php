<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Point;

#[SolutionAttribute(
    name: 'Beacon Exclusion Zone',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        // calculate min, max x to scan range
        //
        $minX = PHP_INT_MAX;
        $maxX = PHP_INT_MIN;
        /** @var Sensor[] $sensors */
        $sensors = [];
        $lineToCheck = null;

        foreach ($input->asArray() as $row) {
            if (preg_match('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/', $row, $matches)) {
                [, $sx, $sy, $bx, $by] = $matches;

                $sensors[] = new Sensor(new Point((int) $sx, (int) $sy), new Point((int) $bx, (int) $by));
                $minX = min($minX, (int) $sx, (int) $bx);
                $maxX = max ($maxX, (int) $sx, (int) $bx);
            } elseif(preg_match('/^y=(\d+)$/', $row, $matches)) {
                $lineToCheck = (int) $matches[1];
            }
        }
        $count = 0;

        var_dump($lineToCheck, $minX, $maxX);

        foreach (range($minX, $maxX) as $x) {
            var_dump($maxX - $x);
            if ($this->cannotPlaceBeaconHere($sensors, $x, $lineToCheck)) {
                $count++;
            }
        }


        var_dump($count);
    
        return new Result($count);
    }

    /**
     * @param Sensor[] $sensors
     */
    private function cannotPlaceBeaconHere(array $sensors, int $x, int $y): bool
    {
        $point = new Point($x, $y);

        foreach ($sensors as $sensor) {
            if ($sensor->isInRange($point)) {
                return true;
            }

            if ($sensor->isBeaconPosition($point)) {
                return true;
            }
        }

        return false;
    }
}
