<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day20;

use AdventOfCode2022\Day20\MixingList;
use PHPUnit\Framework\TestCase;

class MixingListTest extends TestCase
{
    public function testMoveExample(): void
    {
        $list = new MixingList([1, 2, -3, 3, -2, 0, 4]);
        self::assertEquals([1, 2, -3, 3, -2, 0, 4], $list->asArrayOfInt());

        $list->move(); // 1
        self::assertEquals([2, 1, -3, 3, -2, 0, 4], $list->asArrayOfInt());

        $list->move(); // 2
        self::assertEquals([1, -3, 2, 3, -2, 0, 4], $list->asArrayOfInt());

        $list->move(); // -3
        self::assertEquals([1, 2, 3, -2, -3, 0, 4], $list->asArrayOfInt());

        $list->move(); // 3
        self::assertEquals([1, 2, -2, -3, 0, 3, 4], $list->asArrayOfInt());

        $list->move(); // -2
        self::assertEquals([1, 2, -3, 0, 3, 4, -2], $list->asArrayOfInt());

        $list->move(); // 0
        self::assertEquals([1, 2, -3, 0, 3, 4, -2], $list->asArrayOfInt());

        $list->move(); // 4
        self::assertEquals([1, 2, -3, 4, 0, 3, -2], $list->asArrayOfInt());
    }

    /**
     * @dataProvider moveData
     */
    public function testName2(array $start, array $expected, int $currentMove): void
    {
        $list = new MixingList($start, $currentMove);

        $list->move();

        self::assertEquals($expected, $list->asArrayOfInt());
    }

    public function moveData(): iterable
    {
        yield 'One forward' => [
            [1, 2, -3, 3, -2, 0, 4],
            [2, 1, -3, 3, -2, 0, 4],
            1,
        ];

        yield 'Two forward' => [
            [1, 2, -3, 3, -2, 0, 4],
            [1, -3, 3, 2,-2, 0, 4],
            2,
        ];

        yield 'Circural on right' => [
            [1, 2, -3, -2, 3, 0, 4],
            [1, 3, 2, -3, -2, 0, 4],
            5,
        ];

        yield 'Circural right to beginning' => [ // or should be end?
            [1, 2, -3, 3, -2, 0, 4],
            [3, 1, 2, -3, -2, 0, 4],
            4,
        ];

        yield 'Backward ' => [
            [1, 2, 3, -3, -2, 0, 4],
            [1, 2, -2, 3, -3, 0, 4],
            5,
        ];

        yield 'Backward circural' => [
            [1, -3, 2, 3, -2, 0, 4],
            [1, 2, 3, -2, -3, 0, 4],
            2
        ];

        yield '0 does not move' => [
            [1, 2, 3, -2, -3, 0, 4],
            [1, 2, 3, -2, -3, 0, 4],
            6
        ];

        yield 'Circural twice to right' => [
            [1, 2, 3, -2, -3, 0, 7],
            [7, 1, 2, 3, -2, -3, 0],
            7
        ];

        yield 'Circural twice to left' => [
            [-8, 2, 3, -2, -3, 0, 1],
            [2, 3, -2, -3, 0, -8, 1],
            1
        ];

        yield 'Circural twice to left to end' => [
            [-7, 2, 3, -2, -3, 0, 1],
            [2, 3, -2, -3, 0, 1, -7],
            1
        ];

        yield 'Example position forward' => [
            [4, 5, 6, 1, 7, 8, 9],
            [4, 5, 6, 7, 1, 8, 9],
            4
        ];

        yield 'Example position backward' => [
            [4, -2, 5, 6, 7, 8, 9],
            [4, 5, 6, 7, 8, -2, 9],
            2
        ];
    }
}
