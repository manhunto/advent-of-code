<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\InputType;
use App\Utils\Direction;
use App\Utils\Point;

enum CubeTemplate: int
{
    case EXAMPLE_INPUT = 1;
    case PUZZLE_INPUT = 2;

    public function getBluePrint(): array
    {
        return match ($this) {
            self::EXAMPLE_INPUT => [
                [' ', ' ', 'U', ' '],
                ['D', 'L', 'F', ' '],
                [' ', ' ', 'B', 'R']
            ],
            self::PUZZLE_INPUT => [
                [' ', 'U', 'R'],
                [' ', 'F', ' '],
                ['F', 'D', ' '],
                ['B', ' ', ' ']
            ],
            default => throw new \LogicException('Unrecognized template')
        };
    }

    public static function getForInputType(InputType $inputType): self
    {
        if ($inputType->isExample()) {
            return self::EXAMPLE_INPUT;
        }

        return self::PUZZLE_INPUT;
    }

    public function getCurrentFace(Point $position, int $sizeOfSideEdge)
    {
        $bluePrint = $this->getBluePrint();
//        $columns = count($bluePrint) - 1;
//        $rows = count($bluePrint[0]) - 1;

        $x = (int) floor($position->x / $sizeOfSideEdge);
        $y = (int) floor($position->y / $sizeOfSideEdge);

        return $bluePrint[$y][$x];
    }

    public function getFaceInDirection(string $currentFace, Direction $direction): string
    {
        if ($currentFace === 'U') {
            return match ($direction) {
                Direction::WEST => 'L',
                Direction::EAST => throw new \Exception('To be implemented'),
                Direction::SOUTH => throw new \Exception('To be implemented'),
                Direction::NORTH => throw new \Exception('To be implemented')
            };
        }
    }
}
