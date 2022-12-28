<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Direction;
use App\Utils\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testDistanceInManhattanGeometry(): void
    {
        $point = new Point(0, 0);
        $point2 = new Point(2, 2);

        self::assertSame(4, $point->distanceInManhattanGeometry($point2));
    }

    /**
     * @dataProvider getDirectionData
     */
    public function testGetDirection(Point $A, Point $B, Direction $expected): void
    {
        $dir = $A->getDirection($B);

        self::assertEquals($expected, $dir);
    }

    public function getDirectionData(): iterable
    {
        yield [new Point(1, 1), new Point(2, 1), Direction::EAST];
        yield [new Point(3, 1), new Point(1, 1), Direction::WEST];
        yield [new Point(3, 4), new Point(3, 5), Direction::SOUTH];
        yield [new Point(3, 7), new Point(3, 2), Direction::NORTH];
    }
}
