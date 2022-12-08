<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day08;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Treetop Tree House',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $grid = array_map(static fn(string $row) => str_split($row), $input->asArray());
        $isVisible = 0;

        $width = count($grid);
        foreach ($grid as $y => $row) {
            $height = count($row);
            foreach ($row as $x => $tree) {

                $tree = (int) $tree;
                // edge
                if ($y === $height - 1 || $x === $width - 1 || $y === 0 || $x === 0) {
                    $isVisible++;
                } else {

                    $treesOnWest = array_slice($row, 0, $x);
                    $treesOnEast = array_slice($row, $x + 1);
                    $column = array_column($grid, $x);
                    $treesOnNorth = array_slice($column, 0, $y);
                    $treesOnSouth = array_slice($column, $y + 1);

                    $treesOnSides = [$treesOnNorth, $treesOnEast, $treesOnWest, $treesOnSouth];

                    foreach ($treesOnSides as $treesOnSide) {
                        if (max($treesOnSide) < $tree) {
                            $isVisible++;

                            break;
                        }
                    }
                }
            }
        }

        return new Result($isVisible);
    }
}
