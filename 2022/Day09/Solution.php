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
        $tail = new MovingPoint();

        foreach ($input->asArray() as $row) {
            [$direction, $steps] = explode(' ', $row);

            for ($i = 0; $i < $steps; $i++) {
                switch ($direction) {
                    case 'R' :
                        $head->moveRight();
                        break;
                    case 'L':
                        $head->moveLeft();
                        break;
                    case 'U' :
                        $head->moveUp();
                        break;
                    case 'D':
                        $head->moveDown();
                        break;
                }

                $tail->moveTowards($head);
            }
        }
    
        return new Result($tail->countVisitedPointAtLeastOnce());
    }

    private function print(MovingPoint $head, MovingPoint $tail): void
    {
        $grid = [];
        for ($i = 0; $i < 10 ; $i++) {
            $grid[] = array_fill(0, 10, '.');
        }

        $invertY = static fn (int $point): int => 10 - 1 - $point;

        $grid[$invertY(0)][0] = 's';
        $grid[$invertY($head->y)][$head->x] = 'H';
        $grid[$invertY($tail->y)][$tail->x] = 'T';

        foreach ($grid as $row) {
            foreach ($row as $point) {
                echo $point;
            }
            echo PHP_EOL;
        }

        echo 'Head: ' . $head->x . ',' . $head->y . PHP_EOL;
        echo 'Tail: ' . $tail->x . ',' . $tail->y . PHP_EOL;
        echo PHP_EOL;
    }
}
