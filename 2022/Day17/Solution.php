<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day17;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Map;

#[SolutionAttribute(
    name: 'Pyroclastic Flow',
)]
final class Solution implements Solver
{
    private const SPACE_FROM_BOTTOM = 3;

    public function solve(Input $input): Result
    {
        $towerHeightPartOne = $this->simulateForNShapes($input, 2022);
//        $towerHeightPartTwo = $this->simulateForNShapes($input, 1_000_000_000_000);

        return new Result($towerHeightPartOne);
    }

    private function simulateForNShapes(Input $input, int $maxShapes): mixed
    {
        $shapeNumber = 0;
        $movements = $input->asChars();
        $movementNumber = 0;
        $towerHeight = 0;

        $map = Map::generateFilled(1, 6, '#');
        $map->cropOnUp(3, '.');

        do {
            $shape = Shape::createWithShapeNumber($shapeNumber, $towerHeight + self::SPACE_FROM_BOTTOM + 1);
            $map->cropOnUp($shape->getHeight(), '.'); // todo crop to size

            do {
//                $map->printer()
//                    ->drawTemporaryShape($shape->onlySolid(), '@')
//                    ->print();
//
//                readline();

                $movement = $movements[$movementNumber % count($movements)];
//                var_dump($movementNumber);
                $movementNumber++;

                $test = clone $shape;

                if ($movement === '>') {
                    $test->moveRight();

                    if ($test->collide($map) === false) {
                        $shape->moveRight();
                    }
                } else {
                    $test->moveLeft();
                    if ($test->collide($map) === false) {
                        $shape->moveLeft();
                    }
                }

//                $map->printer()
//                    ->drawTemporaryShape($shape->onlySolid(), '@')
//                    ->print();
//
//                readline();
//

                $test = clone $shape;
                $test->fall();

                if ($test->collide($map)) {
                    break;
                }

                $shape->fall();
            } while (true);

            $map->drawShape($shape->asArrayOnlyWithShape(), '#');

//            $map->printer()
//                ->print();
//
//            readline();

            $towerHeight = max($towerHeight, $shape->getMaxY());
            $shapeNumber++;
        } while ($shapeNumber < $maxShapes);
        return $towerHeight;
    }
}
