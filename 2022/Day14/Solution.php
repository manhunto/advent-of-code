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
        $grid = $this->getGrid($input);
        $this->addRocksFromInput($input, $grid);

        $sandProduced = 0;

        while (false === $grid->addSand()) {
            $sandProduced++;
        }

//        $grid->print();

        return new Result($sandProduced);
    }

    private function getGrid(Input $input): Grid
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

        return Grid::generateEmpty($minX - 1, $maxX + 1, $maxY + 2);
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
}
