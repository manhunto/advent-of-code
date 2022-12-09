<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day09;

use AdventOfCode2022\Day09\Knot;
use PHPUnit\Framework\TestCase;

class KnotTest extends TestCase
{
    /**
     * @dataProvider moveTowardsData
     */
    public function testMoveTowards(array $head, array $tail, array $expected): void
    {
        $head = new Knot($head[0],$head[1]);
        $tail = new Knot($tail[0], $tail[1]);

        $tail->moveTowards($head);

        self::assertSame($expected[0], $tail->x);
        self::assertSame($expected[1], $tail->y);
    }

    public function moveTowardsData(): iterable
    {
        // R4
        yield 'Initial' => [[0,0], [0,0], [0,0]];
        yield 'R4 1' => [[1,0], [0,0], [0,0]];
        yield 'R4 2' => [[2,0], [0,0], [1,0]];
        yield 'R4 3' => [[3,0], [1,0], [2,0]];
        yield 'R4 4' => [[4,0], [2,0], [3,0]];
        // U4
        yield 'U4 1' => [[4,1], [3,0], [3,0]];
        yield 'U4 2' => [[4,2], [3,0], [4,1]];
        yield 'U4 3' => [[4,3], [4,1], [4,2]];
        yield 'U4 4' => [[4,4], [4,2], [4,3]];
        // L3
        yield 'L3 1' => [[3,4], [4,3], [4,3]];
        yield 'L3 2' => [[2,4], [4,3], [3,4]];
        yield 'L3 3' => [[1,4], [3,4], [2,4]];
        // D1
        yield 'D1' => [[1,3], [2,4], [2,4]];
        // R4
        yield '2R4 1' => [[2,3], [2,4], [2,4]];
        yield '2R4 2' => [[3,3], [2,4], [2,4]];
        yield '2R4 3' => [[4,3], [2,4], [3,3]];
        yield '2R4 4' => [[5,3], [3,3], [4,3]];
        // D1
        yield '2D1 1' => [[5,2], [4,3], [4,3]];
        // L5
        yield 'L5 1' => [[4,2], [4,3], [4,3]];
        yield 'L5 2' => [[3,2], [4,3], [4,3]];
        yield 'L5 3' => [[2,2], [4,3], [3,2]];
        yield 'L5 4' => [[1,2], [3,2], [2,2]];
        // R2
        yield 'R2 1' => [[2,2], [2,2], [2,2]];
        yield 'R2 2' => [[3,2], [2,2], [2,2]];
    }
}
