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
        $head = new Knot();
        $knots = $this->fillArrayWithKnots(9);

        foreach ($input->asArray() as $row) {
            [$direction, $steps] = explode(' ', $row);

            for ($i = 0; $i < $steps; $i++) {
                $head->moveInDirection($direction);

                /** @var Knot[] $allKnots */
                $allKnots = [$head, ...$knots];
                foreach ($allKnots as $index => $knot) {
                    $knotBefore = $allKnots[$index - 1] ?? null;

                    if ($knotBefore) {
                        $knot->moveTowards($knotBefore);
                    }
                }
            }
        }

        $head = reset($knots);
        $lastKnot = end($knots);
    
        return new Result(
            $head->countPositionsVisitedAtLeastOnce(),
            $lastKnot->countPositionsVisitedAtLeastOnce()
        );
    }

    private function print(Knot $head, Knot ...$tails): void
    {
        $grid = [];
        $gridSize = 10;
        for ($i = 0; $i < $gridSize; $i++) {
            $grid[] = array_fill(0, $gridSize, '.');
        }

        $invertY = static fn (int $point): int => $gridSize - 1 - $point;

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
     * @return Knot[]
     */
    private function fillArrayWithKnots(int $ropeLength): array
    {
        $knots = [];
        for ($i = 0; $i < $ropeLength; $i++) {
            $knots[] = new Knot();
        }

        return $knots;
    }
}
