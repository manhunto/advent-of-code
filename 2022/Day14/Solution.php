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
        $grid = $this->generateGridWithRocks($input);

        return $this->pourSand($grid);
    }

    private function solveSecondPart(Input $input): int
    {
        $grid = $this->generateGridWithRocks($input);
        $grid->addFloor();

        return $this->pourSand($grid);
    }

    private function generateGridWithRocks(Input $input): Grid
    {
        $grid = $this->generateEmptyGrid($input);
        $this->addRocksFromInput($input, $grid);

        return $grid;
    }

    private function generateEmptyGrid(Input $input): Grid
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

        return Grid::generateEmpty($minX - 1, $maxX + 1, $maxY + 3);
    }

    private function addRocksFromInput(Input $input, Grid $grid): void
    {
        foreach ($input->asArray() as $row) {
            $rockLineWaypoints = explode(' -> ', $row);
            $prev = array_shift($rockLineWaypoints);

            foreach ($rockLineWaypoints as $item) {
                [$prevX, $prevY] = explode(',', $prev);
                [$nextX, $nextY] = explode(',', $item);
                $prev = $item;

                $grid->addRock((int)$prevX, (int)$prevY, (int)$nextX, (int)$nextY);
            }
        }
    }

    private function pourSand(Grid $grid): int
    {
        while (false === $grid->pourSand()) {}

        return $grid->countSandInCave();
    }
}
