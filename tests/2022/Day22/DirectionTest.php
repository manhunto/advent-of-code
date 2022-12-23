<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day22;

use AdventOfCode2022\Day22\Direction;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    public function testTurnClockwise(): void
    {
        $dir = Direction::RIGHT;

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::DOWN, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::LEFT, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::UP, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::RIGHT, $dir);

        $dir = $dir->turnClockwise();
        self::assertEquals(Direction::DOWN, $dir);
    }

    public function testTurnAntiClockwise(): void
    {
        $dir = Direction::UP;

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::LEFT, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::DOWN, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::RIGHT, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::UP, $dir);

        $dir = $dir->turnAntiClockwise();
        self::assertEquals(Direction::LEFT, $dir);
    }
}
