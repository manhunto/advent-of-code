<?php

declare(strict_types=1);

/**
 * @see https://adventofcode.com/2022/day/2
 */

namespace AdventOfCode2022\Day02;

use App\Input;
use App\Result;
use App\Solver;

final class Solution implements Solver
{
    private const LOOSE = 'X';
    private const WIN = 'Z';

    public function solve(Input $input): Result
    {
        $totalSum1 = 0;
        $totalSum2 = 0;

        foreach ($input->asArray() as $singleGameGuide) {
            [$oponent, $response] = explode(' ', $singleGameGuide);

            $oponentShape = Shape::decryptShape($oponent);
            $responseShape = Shape::decryptShape($response);

            if ($response === self::LOOSE) {
                $responseShape2 = $oponentShape->getShapeThatIDefeat();
            } elseif ($response === self::WIN) {
                $responseShape2 = $oponentShape->getShapeThatDefeatsMe();
            } else {
                $responseShape2 = $oponentShape;
            }

            $totalSum1 += $this->calculatePoints($responseShape, $oponentShape);
            $totalSum2 += $this->calculatePoints($responseShape2, $oponentShape);
        }

        return new Result($totalSum1, $totalSum2);
    }

    private function calculatePoints(Shape $responseShape, Shape $oponentShape): int
    {
        $points = $responseShape->value;

        if ($responseShape->wins($oponentShape)) {
            $points += 6;
        } elseif ($responseShape === $oponentShape) {
            $points += 3;
        }

        return $points;
    }
}
