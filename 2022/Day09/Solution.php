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
