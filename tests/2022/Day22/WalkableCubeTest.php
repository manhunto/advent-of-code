<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day22;

use AdventOfCode2022\Day22\CubeEdgeMapper;
use AdventOfCode2022\Day22\WalkableCube;
use App\Utils\DirectionalLocation;
use App\Utils\Map;
use PHPUnit\Framework\TestCase;

class WalkableCubeTest extends TestCase
{
    /**
     * @dataProvider exampleInputData
     */
    public function testExampleInputCube(DirectionalLocation $origin, DirectionalLocation $expected): void
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
        $mapper = new CubeEdgeMapper($map, ['o']);
        $cube = new WalkableCube(['o'], $map, ['o'], $mapper->getEdgeMap());

        $nextMove = $cube->getNextPosition($origin);

        self::assertEquals($expected, $nextMove);
    }

    public function exampleInputData(): iterable
    {
        yield 'Not on edge 1' => [DirectionalLocation::south(7, 1), DirectionalLocation::south(7,2)];
        yield 'Not on edge 2' => [DirectionalLocation::east(1, 4), DirectionalLocation::east(2, 4)];
        yield 'Not on edge 3' => [DirectionalLocation::west(4, 4), DirectionalLocation::west(3, 4)];
        yield 'Not on edge 4' => [DirectionalLocation::north(7,4), DirectionalLocation::north(7,3)];
        yield 'Not on edge 5' => [DirectionalLocation::south(7,7), DirectionalLocation::south(7,8)];
        yield 'Not on edge 6' => [DirectionalLocation::west(10, 7),DirectionalLocation::west(9, 7)];

        yield 'Edge possible through map 1' => [DirectionalLocation::south(6,2), DirectionalLocation::south(6,3)];
        yield 'Edge possible through map 2' => [DirectionalLocation::south(7,2), DirectionalLocation::south(7,3)];
        yield 'Edge possible through map 3' => [DirectionalLocation::west(8, 2), DirectionalLocation::west(7, 2)];
        yield 'Edge possible through map 4' => [DirectionalLocation::east(6, 3), DirectionalLocation::east(7, 3)];
        yield 'Edge possible through map 5' => [DirectionalLocation::east(2, 3), DirectionalLocation::east(3, 3)];
        yield 'Edge possible through map 6' => [DirectionalLocation::east(6, 2), DirectionalLocation::east(7, 2)];

        yield 'From edge of side 1 to side 3 #1' => [DirectionalLocation::west(6, 2), DirectionalLocation::south(5, 3)];
        yield 'From edge of side 1 to side 3 #2' => [DirectionalLocation::west(6, 1), DirectionalLocation::south(4, 3)];
        yield 'From edge of side 1 to side 3 #3' => [DirectionalLocation::west(6, 0), DirectionalLocation::south(3, 3)];

        yield 'Into abyss #1' => [DirectionalLocation::north(6, 0), DirectionalLocation::south(2, 3)];
    }
}
