<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day09;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Rope Bridge',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $head = new MovingPoint();
        $tails = $this->fillArrayWithTails(9);

        foreach ($input->asArray() as $row) {
            [$direction, $steps] = explode(' ', $row);

            for ($i = 0; $i < $steps; $i++) {
                $head->moveInDirection($direction);

                $allKnots = [$head, ...$tails];
                foreach ($allKnots as $index => $knot) {
                    $knotBefore = $allKnots[$index - 1] ?? null;

                    if ($knotBefore) {
                        $knot->moveTowards($knotBefore);
                    }
                }
            }
        }

        $firstTail = reset($tails);
        $lastKnot = end($tails);
    
        return new Result(
            $firstTail->countVisitedPointAtLeastOnce(),
            $lastKnot->countVisitedPointAtLeastOnce()
        );
    }

    private function print(MovingPoint $head, MovingPoint ...$tails): void
    {
        $grid = [];
        for ($i = 0; $i < 10 ; $i++) {
            $grid[] = array_fill(0, 10, '.');
        }

        $invertY = static fn (int $point): int => 10 - 1 - $point;

        $grid[$invertY(0)][0] = 's';
        $grid[$invertY($head->y)][$head->x] = 'H';

        foreach ($tails as $index => $tail) {
            $grid[$invertY($tail->y)][$tail->x] = $index + 1;
        }

        foreach ($grid as $row) {
            foreach ($row as $point) {
                echo $point;
            }
            echo PHP_EOL;
        }

        echo 'Head: ' . $head->x . ',' . $head->y . PHP_EOL;

        foreach ($tails as $index => $tail) {
            echo $index . ': ' . $tail->x . ',' . $tail->y . PHP_EOL;
        }

        echo PHP_EOL;
    }

    /**
     * @return MovingPoint[]
     */
    private function fillArrayWithTails(int $tailsCount): array
    {
        $tails = [];
        for ($i = 0; $i < $tailsCount; $i++) {
            $tails[] = new MovingPoint();
        }

        return $tails;
    }
}
