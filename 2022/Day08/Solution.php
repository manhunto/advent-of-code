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
        $scenicScores = [];

        $width = count($grid);
        foreach ($grid as $y => $row) {
            $height = count($row);
            foreach ($row as $x => $tree) {
                $tree = (int) $tree;

                // edge
                if ($y === $height - 1 || $x === $width - 1 || $y === 0 || $x === 0) {
                    $isVisible++;
                } else {

                    $treesOnSides = $this->getTreesOnSides($row, $x, $grid, $y);

                    if ($this->isTreeVisibleFromOutsideTheGrid($tree, $treesOnSides)) {
                        $isVisible++;
                    }

                    $scenicScores[] = $this->calculateScenicScore($tree, $treesOnSides);
                }
            }
        }

        return new Result($isVisible, max($scenicScores));
    }

    private function calculateScenicScore(int $tree, array $treesOnSides): int
    {
        $scenicScoresOnSides = array_map(fn (array $row) => $this->calculateScenicScoreForRow($row, $tree), $treesOnSides);

        return array_reduce($scenicScoresOnSides, static fn (int $result, int $score) => $result * $score, 1);
    }

    private function calculateScenicScoreForRow(array $treesOnSide, int $tree): int
    {
        foreach ($treesOnSide as $index => $item) {
            if ($item >= $tree) {
                return $index + 1;
            }
        }

        return count($treesOnSide);
    }

    private function isTreeVisibleFromOutsideTheGrid(int $tree, array $treesOnSides): bool
    {
        foreach ($treesOnSides as $treesOnSide) {
            if (max($treesOnSide) < $tree) {
                return true;
            }
        }

        return false;
    }

    private function getTreesOnSides(array $row, int $x, array $grid, int $y): array
    {
        $treesOnWest = array_reverse(array_slice($row, 0, $x));
        $treesOnEast = array_slice($row, $x + 1);
        $column = array_column($grid, $x);
        $treesOnNorth = array_reverse(array_slice($column, 0, $y));
        $treesOnSouth = array_slice($column, $y + 1);

        return [$treesOnNorth, $treesOnEast, $treesOnWest, $treesOnSouth];
    }
}
