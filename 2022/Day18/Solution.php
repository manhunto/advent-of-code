<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day18;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Boiling Boulders',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $grid3d = [];

        foreach ($input->asArray() as $row) {
            [$x, $y, $z] = explode(',', $row);
            $grid3d[$x][$y][$z] = 1;
        }

        $surface = $this->calculateWholeAre($grid3d);

        return new Result($surface);
    }

    private function calculateWholeAre(array $grid3d): int
    {
        $adjecentsGrid = [
            [1, 0, 0],
            [0, 1, 0],
            [0, 0, 1],
            [-1, 0, 0],
            [0, -1, 0],
            [0, 0, -1],
        ];

        $surface = 0;
        foreach ($grid3d as $x => $xies) {
            foreach ($xies as $y => $yies) {
                foreach ($yies as $z => $value) {
                    foreach ($adjecentsGrid as $adjecentGrid) {
                        $adjecent = $grid3d[$x + $adjecentGrid[0]][$y + $adjecentGrid[1]][$z + $adjecentGrid[2]] ?? 0;

                        if ($adjecent === 0) {
                            $surface++;
                        }
                    }
                }
            }
        }

        return $surface;
    }
}
