<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Utils\Location;
use App\Utils\Range;
use PHPUnit\Framework\TestCase;

class SensorTest extends TestCase
{
    /**
     * @dataProvider  coveredPointsInLine
     */
    public function testGetRangeOnLine(int $y, ?Range $expected): void
    {
        $sensor = new Sensor(new Location(0, 0), new Location(1, 3));

        self::assertEquals($expected, $sensor->getRangeOnLine($y));
    }

    public function coveredPointsInLine(): iterable
    {
        yield [-1, new Range(-3, 3)];
        yield [-2, new Range(-2, 2)];
        yield [-3, new Range(-1, 1)];
        yield [-4, Range::createForPoint(0)];
        yield [-5, null];
        yield [-412, null];
        yield [0, new Range(-4, 4)];
        yield [1, new Range(-3, 3)];
        yield [2, new Range(-2, 2)];
        yield [3, new Range(-1, 1)];
        yield [4, Range::createForPoint(0)];
        yield [5, null];
        yield [27, null];
    }

    /**
     * @dataProvider getPointsOnBorderPlusOneData
     */
    public function testGetPointsOnBorderPlusOne(Range $inRange, array $expected): void
    {
        $sensor = new Sensor(new Location(0, 0), new Location(1, 3));
        $pointsOnBorder = $sensor->getPointsOnBorderPlusOne($inRange);

        self::assertEquals($expected, iterator_to_array($pointsOnBorder));
    }

    public function getPointsOnBorderPlusOneData(): iterable
    {
        yield [
            new Range(0, 10),
            [
                new Location(5, 0),
                new Location(4, 1),
                new Location(3, 2),
                new Location(2, 3),
                new Location(1, 4),
                new Location(0, 5),
            ]
        ];

        yield [
            new Range(1, 10),
            [
                new Location(4, 1),
                new Location(3, 2),
                new Location(2, 3),
                new Location(1, 4),
            ]
        ];

        yield [
            new Range(-1, 10),
            [
                new Location(4, -1),
                new Location(5, 0),
                new Location(4, 1),
                new Location(3, 2),
                new Location(2, 3),
                new Location(-1, 4),
                new Location(1, 4),
                new Location(0, 5),
            ]
        ];

        yield 'All points in range' => [
            new Range(-10, 10),
            [
                new Location(0, -5),
                new Location(-1, -4),
                new Location(1, -4),
                new Location(-2, -3),
                new Location(2, -3),
                new Location(-3, -2),
                new Location(3, -2),
                new Location(-4, -1),
                new Location(4, -1),
                new Location(-5, 0),
                new Location(5, 0),
                new Location(-4, 1),
                new Location(4, 1),
                new Location(-3, 2),
                new Location(3, 2),
                new Location(-2, 3),
                new Location(2, 3),
                new Location(-1, 4),
                new Location(1, 4),
                new Location(0, 5),
            ]
        ];

        yield 'Not in range' => [
            new Range(10, 20),
            []
        ];
    }
}
