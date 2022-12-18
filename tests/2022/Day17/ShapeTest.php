<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day17;

use AdventOfCode2022\Day17\Shape;
use PHPUnit\Framework\TestCase;

class ShapeTest extends TestCase
{
    public function testItCreatesShapeInInitRow(): void
    {
        $s = new Shape([[0, 0, 1, 1, 1, 1, 0]], 3);

        self::assertEquals([3 => [0, 0, 1, 1, 1, 1, 0]], $s->asArray());
    }

    public function testDoesNotMoveToRightIfThereIsBoundary(): void
    {
        $s = new Shape([[0, 0, 1, 1, 1, 1, 0]], 3);
        $s->moveRight();

        self::assertEquals([3 => [0, 0, 0, 1, 1, 1, 1]], $s->asArray());
        $s->moveRight();

        self::assertEquals([3 => [0, 0, 0, 1, 1, 1, 1]], $s->asArray());
        $s->moveRight();

        self::assertEquals([3 => [0, 0, 0, 1, 1, 1, 1]], $s->asArray());
    }

    public function testDoesNotMoveToLeftIfThereIsBoundary(): void
    {
        $s = new Shape([[0, 0, 1, 1, 1, 1, 0]], 3);
        $s->moveLeft();

        self::assertEquals([3 => [0, 1, 1, 1, 1, 0, 0]], $s->asArray());
        $s->moveLeft();

        self::assertEquals([3 => [1, 1, 1, 1, 0, 0, 0]], $s->asArray());
        $s->moveLeft();

        self::assertEquals([3 => [1, 1, 1, 1, 0, 0, 0]], $s->asArray());
    }

    public function testDoesNotMoveToRightIfThereIsBoundaryForBlock(): void
    {
        $s = new Shape([
            [0, 0, 0, 0, 1, 1, 0],
            [0, 0, 0, 0, 1, 1, 0],
        ], 3);

        $s->moveRight();

        self::assertEquals([
            3 => [0, 0, 0, 0, 0, 1, 1],
            4 => [0, 0, 0, 0, 0, 1, 1],
        ], $s->asArray());

        $s->moveRight();

        self::assertEquals([
            3 => [0, 0, 0, 0, 0, 1, 1],
            4 => [0, 0, 0, 0, 0, 1, 1],
        ], $s->asArray());
    }


    public function testFall(): void
    {
        $s = new Shape([
            [0, 0, 0, 0, 1, 1, 0],
            [0, 0, 0, 0, 1, 1, 0],
        ], 3);

        $s->fall();

        self::assertEquals([
            3 => [0, 0, 0, 0, 1, 1, 0],
            2 => [0, 0, 0, 0, 1, 1, 0],
        ], $s->asArray());

        $s->fall();

        self::assertEquals([
            2 => [0, 0, 0, 0, 1, 1, 0],
            1 => [0, 0, 0, 0, 1, 1, 0],
        ], $s->asArray());
    }

    public function testCollide(): void
    {
        $s = new Shape([
            [0, 0, 0, 0, 1, 1, 0],
            [0, 0, 0, 0, 1, 1, 0],
        ], 2);

        $map = [
            1 => [1,1,1,1,1,1,1]
        ];

        self::assertFalse($s->collide($map));

        $s->fall();

        self::assertTrue($s->collide($map));
    }

    public function testGetMaxY(): void
    {
        $s = new Shape([
            [0, 0, 0, 0, 1, 1, 0],
            [0, 0, 0, 0, 1, 1, 0],
        ], 4);

        self::assertSame(5, $s->getMaxY());

        $s->fall();

        self::assertSame(4, $s->getMaxY());
    }
}
