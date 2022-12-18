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
    private const MOVING_ROCK = '@';
    private const AIR = '.';
    private const SOLID_ROCK = '#';

    public function solve(Input $input): Result
    {
        $towerHeightPartOne = $this->simulateForNShapes($input, 2022);
        $towerHeightPartTwo = $this->simulateForNShapes($input, 1_000_000_000_000);

        return new Result($towerHeightPartOne, $towerHeightPartTwo);
    }

    private function simulateForNShapes(Input $input, int $maxShapes): mixed
    {
        $shapeNumber = 0;
        $movements = $input->asChars();
        $movementNumber = 0;
        $towerHeight = 0;

        $movementHistory = [];
        $firstDetectedMovement = null;
        $wasCycleAdded = false;

        $map = Map::generateFilled(1, 6, self::SOLID_ROCK);
        $map->cropOnUp(3, self::AIR);

        do {
            $shape = Shape::createWithShapeNumber($shapeNumber, $towerHeight + self::SPACE_FROM_BOTTOM + 1);
            $map->cropOnUpToHeight($towerHeight + self::SPACE_FROM_BOTTOM + $shape->getHeight(), self::AIR);

            $individualShapeMove = 0;
            $canFall = true;

            $historyKey = $shapeNumber % 5 . '/' . $movementNumber % count($movements);

            if ($wasCycleAdded === false && isset($movementHistory[$historyKey])) {
                if ($firstDetectedMovement === null)  {
                    $firstDetectedMovement = $historyKey;
                } elseif ($firstDetectedMovement === $historyKey) {
                    $fromHist = $movementHistory[$historyKey];
                    $cycleHeight = $towerHeight - $fromHist['height'];
                    $cycleShapeNumbers = $shapeNumber - $fromHist['shape-number'];

                    $cyclesLeft = floor(($maxShapes - $shapeNumber )/ $cycleShapeNumbers);

                    $towerHeightBefore = $towerHeight;

                    $towerHeight += (int) $cyclesLeft * $cycleHeight;
                    $shapeNumber += (int) $cyclesLeft * $cycleShapeNumbers;

//                    $map->printer()
//                        ->naturalHorizontally()
//                        ->drawTemporaryShape($shape->asArrayOnlyWithShape(), self::MOVING_ROCK)
//                        ->print();
//
//                    var_dump($towerHeightBefore);
//                    var_dump($towerHeight);
//
//
//                    readline();


                    $map->moveRowsTo($towerHeight - $towerHeightBefore);
                    $wasCycleAdded = true;
                    $shape = Shape::createWithShapeNumber($shapeNumber, $towerHeight + self::SPACE_FROM_BOTTOM + 1);
                    $map->cropOnUpToHeight($towerHeight + self::SPACE_FROM_BOTTOM + $shape->getHeight(), self::AIR);

//                    $map->printer()
//                            ->naturalHorizontally()
//                        ->drawTemporaryShape($shape->asArrayOnlyWithShape(), self::MOVING_ROCK)
//                        ->print();
//
//                    readline();
                }
            }

            $movementHistory[$historyKey] = [
                'height' => $towerHeight,
                'shape-number' => $shapeNumber,
            ];

            do {
                $movement = $movements[$movementNumber % count($movements)];
                $movementNumber++;
                $individualShapeMove++;


                $shouldTest = $individualShapeMove > self::SPACE_FROM_BOTTOM;

                if ($movement === '>') {
                    if ($shouldTest === false || $shape->canMoveRight($map, self::SOLID_ROCK)) {
                        $shape->moveRight();
                    }
                } else if ($shouldTest === false || $shape->canMoveLeft($map, self::SOLID_ROCK)) {
                    $shape->moveLeft();
                }

                if ($shouldTest && $shape->canFall($map, self::SOLID_ROCK) === false) {
                    $canFall = false;
                } else {
                    $shape->fall();
                }
            } while ($canFall);

            $map->drawShape($shape->asArrayOnlyWithShape(), self::SOLID_ROCK);

            $towerHeight = max($towerHeight, $shape->getMaxY());
            $shapeNumber++;
        } while ($shapeNumber < $maxShapes);
        return $towerHeight;
    }
}
