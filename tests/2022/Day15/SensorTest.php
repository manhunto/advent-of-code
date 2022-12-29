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
}
