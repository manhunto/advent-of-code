<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Direction;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    public function testTurnClockwise(): void
    {
        $dir = Direction::EAST;

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::SOUTH, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::WEST, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::NORTH, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::EAST, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::SOUTH, $dir);
    }

    public function testTurnAntiClockwise(): void
    {
        $dir = Direction::NORTH;

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::WEST, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::SOUTH, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::EAST, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::NORTH, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::WEST, $dir);
    }

    /**
     * @dataProvider reversedData
     */
    public function testReversed(Direction $init, Direction $expected): void
    {
        $reversed = $init->reversed();

        self::assertSame($expected, $reversed);
    }

    public function reversedData(): iterable
    {
        yield 'N->S' => [Direction::NORTH, Direction::SOUTH];
        yield 'S->N' => [Direction::SOUTH, Direction::NORTH];
        yield 'E->W' => [Direction::EAST, Direction::WEST];
        yield 'W->E' => [Direction::WEST, Direction::EAST];
    }
}
