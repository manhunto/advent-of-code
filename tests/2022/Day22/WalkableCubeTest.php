<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day22;

use AdventOfCode2022\Day22\WalkableCube;
use AdventOfCode2022\Day22\CubeTemplate;
use App\Utils\Direction;
use App\Utils\Map;
use App\Utils\Point;
use PHPUnit\Framework\TestCase;

class WalkableCubeTest extends TestCase
{
    /**
     * @dataProvider exampleInputData
     */
    public function testExampleInputCube(Point $currentPoint, Direction $dir, Point $expected): void
    {
        $map = new Map([
            [' ', ' ', ' ', ' ', ' ', ' ', 'o', 'o', 'o', ' ', ' ', ' '],
            [' ', ' ', ' ', ' ', ' ', ' ', 'o', 'o', 'o', ' ', ' ', ' '],
            [' ', ' ', ' ', ' ', ' ', ' ', 'o', 'o', 'o', ' ', ' ', ' '],
            ['o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', ' ', ' ', ' '],
            ['o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', ' ', ' ', ' '],
            ['o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', ' ', ' ', ' '],
            [' ', ' ', ' ', ' ', ' ', ' ', 'o', 'o', 'o', 'o', 'o', 'o'],
            [' ', ' ', ' ', ' ', ' ', ' ', 'o', 'o', 'o', 'o', 'o', 'o'],
            [' ', ' ', ' ', ' ', ' ', ' ', 'o', 'o', 'o', 'o', 'o', 'o'],
        ]);
        $cube = new WalkableCube(CubeTemplate::EXAMPLE_INPUT, 3, ['o'], $map);

        $nextMove = $cube->getNextPosition($currentPoint, $dir);

        self::assertEquals($expected, $nextMove);

    }

    public function exampleInputData(): iterable
    {
        yield 'Not on edge 1' => [new Point(7, 1), Direction::SOUTH, new Point(7, 2)];
        yield 'Not on edge 2' => [new Point(1, 4), Direction::EAST, new Point(2, 4)];
        yield 'Not on edge 3' => [new Point(4, 4), Direction::WEST, new Point(3, 4)];
        yield 'Not on edge 4' => [new Point(7, 4), Direction::NORTH, new Point(7, 3)];
        yield 'Not on edge 5' => [new Point(7, 7), Direction::SOUTH, new Point(7, 8)];
        yield 'Not on edge 6' => [new Point(10, 7), Direction::WEST, new Point(9, 7)];

        yield 'Edge possible through map 1' => [new Point(6, 2), Direction::SOUTH, new Point(6, 3)];
        yield 'Edge possible through map 2' => [new Point(7, 2), Direction::SOUTH, new Point(7, 3)];
        yield 'Edge possible through map 3' => [new Point(8, 2), Direction::EAST, new Point(9, 2)];
        yield 'Edge possible through map 4' => [new Point(6, 3), Direction::EAST, new Point(7, 3)];
        yield 'Edge possible through map 5' => [new Point(2, 3), Direction::EAST, new Point(3, 3)];
        yield 'Edge possible through map 6' => [new Point(6, 2), Direction::EAST, new Point(7, 2)];

        yield 'From edge of side 1 to side 3 #1' => [new Point(6, 2), Direction::WEST, new Point(5, 3)];
        yield 'From edge of side 1 to side 3 #2' => [new Point(6, 1), Direction::WEST, new Point(4, 3)];
        yield 'From edge of side 1 to side 3 ##' => [new Point(6, 0), Direction::WEST, new Point(3, 3)];
    }
}
