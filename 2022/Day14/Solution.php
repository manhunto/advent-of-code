<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day14;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Regolith Reservoir',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $sandInCaveFirstPart = $this->solveFirstPart($input);
        $sandInCaveSecondPart = $this->solveSecondPart($input);

        return new Result($sandInCaveFirstPart, $sandInCaveSecondPart);
    }

    private function solveFirstPart(Input $input): int
    {
        $cave = $this->generateCaveWithRocks($input);

        return $this->pourSand($cave);
    }

    private function solveSecondPart(Input $input): int
    {
        $cave = $this->generateCaveWithRocks($input);
        $cave->addFloor();

        return $this->pourSand($cave);
    }

    private function generateCaveWithRocks(Input $input): Cave
    {
        $cave = $this->generateEmptyCave($input);
        $this->addRocksFromInput($input, $cave);

        return $cave;
    }

    private function generateEmptyCave(Input $input): Cave
    {
        $minX = PHP_INT_MAX;
        $maxX = PHP_INT_MIN;
        $maxY = PHP_INT_MIN;

        foreach ($input->asArray() as $row) {
            foreach (explode(' -> ', $row) as $item) {
                [$x, $y] = explode(',', $item);

                $maxY = max($maxY, (int)$y);

                $minX = min($minX, (int)$x);
                $maxX = max($maxX, (int)$x);
            }
        }

        return Cave::generateEmpty($minX - 1, $maxX + 1, $maxY + 3);
    }

    private function addRocksFromInput(Input $input, Cave $cave): void
    {
        foreach ($input->asArray() as $row) {
            $rockLineWaypoints = explode(' -> ', $row);
            $prev = array_shift($rockLineWaypoints);

            foreach ($rockLineWaypoints as $item) {
                [$prevX, $prevY] = explode(',', $prev);
                [$nextX, $nextY] = explode(',', $item);
                $prev = $item;

                $cave->addRock((int)$prevX, (int)$prevY, (int)$nextX, (int)$nextY);
            }
        }
    }

    private function pourSand(Cave $cave): int
    {
        while (false === $cave->pourSand()) {}

        return $cave->countSandInCave();
    }
}
