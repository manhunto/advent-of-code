<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day22;

use AdventOfCode2022\Day22\CubeEdgeMapper;
use AdventOfCode2022\Day22\DirectionFromPoint as FromPoint;
use App\Utils\Map;
use App\Utils\Point;
use PHPUnit\Framework\TestCase;

class CubeEdgeMapperTest extends TestCase
{
    private CubeEdgeMapper $mapper;
    private Map $map;

    protected function setUp(): void
    {
        $this->map = new Map([
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

        $this->mapper = new CubeEdgeMapper($this->map, ['o']);
    }


    public function testGetInnerPoints(): void
    {
        $points = $this->mapper->getEdgeInnerPoints();

        self::assertEquals([
            new Point(6, 3),
            new Point(6, 5),
            new Point(8, 6),
        ], $points);
    }

    public function testGetOutsideEdgePoints(): void
    {
        $points = $this->mapper->getOutsideEdgePoints();

        self::assertCount(42, $points);
    }

    public function testGetEdgeMap(): void
    {
        $expected = [
            // 1
            [FromPoint::west(6, 2), FromPoint::south(5, 3)],
            [FromPoint::north(5, 3), FromPoint::east(6, 2)],

            [FromPoint::west(6, 1), FromPoint::south(4, 3)],
            [FromPoint::north(4, 3), FromPoint::east(6, 1)],

            [FromPoint::west(6, 0), FromPoint::south(3, 3)],
            [FromPoint::north(3, 3), FromPoint::east(6, 0)],

            // 2
            [FromPoint::north(6, 0), FromPoint::south(2, 3)],
            [FromPoint::north(2, 3), FromPoint::south(6, 0)],

            [FromPoint::north(7, 0), FromPoint::south(1, 3)],
            [FromPoint::north(1, 3), FromPoint::south(7, 0)],

            [FromPoint::north(8, 0), FromPoint::south(0, 3)],
            [FromPoint::north(0, 3), FromPoint::south(8, 0)],

            // 3 - from 6,5
            [FromPoint::south(5, 5), FromPoint::east(6, 6)],
            [FromPoint::west(6, 6), FromPoint::north(5, 5)],

            [FromPoint::south(4, 5), FromPoint::east(6, 7)],
            [FromPoint::west(6, 7), FromPoint::north(4, 5)],

            [FromPoint::south(3, 5), FromPoint::east(6, 8)],
            [FromPoint::west(6, 8), FromPoint::north(3, 5)],

            // 4
            [FromPoint::south(2, 5), FromPoint::north(6, 8)],
            [FromPoint::south(6, 8), FromPoint::north(2, 5)],

            [FromPoint::south(1, 5), FromPoint::north(7, 8)],
            [FromPoint::south(7, 8), FromPoint::north(1, 5)],

            [FromPoint::south(0, 5), FromPoint::north(8, 8)],
            [FromPoint::south(8, 8), FromPoint::north(0, 5)],

            // 5
            [FromPoint::west(0, 5), FromPoint::north(9, 8)],
            [FromPoint::south(9, 8), FromPoint::east(0, 5)],

            [FromPoint::west(0, 4), FromPoint::north(10, 8)],
            [FromPoint::south(10, 8), FromPoint::east(0, 4)],

            [FromPoint::west(0, 3), FromPoint::north(11, 8)],
            [FromPoint::south(11, 8), FromPoint::east(0, 3)],

            // 6 - from 8,5
            [FromPoint::east(8, 5), FromPoint::south(9, 6)],
            [FromPoint::north(9, 6), FromPoint::west(8, 5)],

            [FromPoint::east(8, 4), FromPoint::south(10, 6)],
            [FromPoint::north(10, 6), FromPoint::west(8, 4)],

            [FromPoint::east(8, 3), FromPoint::south(11, 6)],
            [FromPoint::north(11, 6), FromPoint::west(8, 3)],

            // 7
            [FromPoint::east(8, 2), FromPoint::west(11, 6)],
            [FromPoint::east(11, 6), FromPoint::west(8, 2)],

            [FromPoint::east(8, 1), FromPoint::west(11, 7)],
            [FromPoint::east(11, 7), FromPoint::west(8, 1)],

            [FromPoint::east(8, 0), FromPoint::west(11, 8)],
            [FromPoint::east(11, 8), FromPoint::west(8, 0)],
        ];

        $edgeMap = $this->mapper->getEdgeMap()->getAll();

        self::assertEquals($expected, $edgeMap);
    }

}
