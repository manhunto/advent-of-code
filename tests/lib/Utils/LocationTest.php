<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Direction;
use App\Utils\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testDistanceInManhattanGeometry(): void
    {
        $point = new Location(0, 0);
        $point2 = new Location(2, 2);

        self::assertSame(4, $point->distanceInManhattanGeometry($point2));
    }

    /**
     * @dataProvider getDirectionData
     */
    public function testGetDirection(Location $A, Location $B, Direction $expected): void
    {
        $dir = $A->getDirection($B);

        self::assertEquals($expected, $dir);
    }

    public function getDirectionData(): iterable
    {
        yield [new Location(1, 1), new Location(2, 1), Direction::EAST];
        yield [new Location(3, 1), new Location(1, 1), Direction::WEST];
        yield [new Location(3, 4), new Location(3, 5), Direction::SOUTH];
        yield [new Location(3, 7), new Location(3, 2), Direction::NORTH];
    }
}
