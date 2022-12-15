<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Utils\Point;
use PHPUnit\Framework\TestCase;

class SensorTest extends TestCase
{
    /**
     * @dataProvider  coveredPointsInLine
     */
    public function testGetCoveredXPositionsInY(int $y, array $expected): void
    {
        $sensor = new Sensor(new Point(0, 0), new Point(1, 3));

        self::assertEquals($expected, $sensor->getCoveredXPositionsInY($y));
    }

    public function coveredPointsInLine(): iterable
    {
        yield [-1, [-3, -2, -1, 0, 1, 2, 3]];
        yield [-2, [-2, -1, 0, 1, 2]];
        yield [-3, [-1, 0, 1]];
        yield [-4, [0]];
        yield [-5, []];
        yield [-412, []];
        yield [0, [-4, -3, -2, -1, 0, 1, 2, 3, 4]];
        yield [1, [-3, -2, -1, 0, 1, 2, 3]];
        yield [2, [-2, -1, 0, 1, 2]];
        yield [3, [-1, 0, 1]];
        yield [4, [0]];
        yield [5, []];
        yield [27, []];
    }
}
