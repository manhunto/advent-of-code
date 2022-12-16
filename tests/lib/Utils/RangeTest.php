<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Range;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    public function testCanCreateOneItemWideRange(): void
    {
        $range = new Range(2, 2);

        self::assertSame(2, $range->from);
        self::assertSame(2, $range->to);
    }

    public function testCannotCreateWhenFromIsGreaterThanTo(): void
    {
        $this->expectException(\LogicException::class);

        new Range(2, 1);
    }

    /**
     * @dataProvider unionData
     */
    public function testUnion(array $A, array $B, array $expected): void
    {
        $range = new Range($A[0], $A[1]);

        $rangeSum = $range->union(new Range($B[0], $B[1]));

        self::assertEquals(new Range($expected[0], $expected[1]), $rangeSum);
    }

    public function unionData(): iterable
    {
        yield 'Widen to left' => [[0, 10], [-2, 4], [-2, 10]];
        yield 'Widen to right' => [[0, 10], [5, 15], [0, 15]];
        yield 'Inside, stay the same' => [[0, 10], [2, 8], [0, 10]];
        yield 'Outside, widen both sides' => [[0, 10], [-20, 13], [-20, 13]];
        yield 'The same' => [[0, 10], [0, 10], [0, 10]];
        yield 'Adjacent on right' => [[0, 5], [6, 10], [0, 10]];
        yield 'Adjacent on left' => [[0, 5], [-5, -1], [-5, 5]];
        yield 'Adjacent on right by one' => [[0, 5], [5, 10], [0, 10]];
        yield 'Adjacent on left by one' => [[0, 5], [-5, 0], [-5, 5]];


        // todo cannot when not colide
    }

    /**
     * @dataProvider cannotUnion
     */
    public function testCannotUnion(array $A, array $B): void
    {
        $range = new Range($A[0], $A[1]);

        $this->expectException(\LogicException::class);

        $range->union(new Range($B[0], $B[1]));
    }

    public function cannotUnion(): iterable
    {
        yield 'Two points space on right, close' => [[0, 10], [12, 15]];
        yield 'Two points space on right, far' => [[0, 10], [100, 200]];
        yield 'Two points space on left, close' => [[0, 10], [5, -2]];
        yield 'Two points space on left, far' => [[0, 10], [-1001, -1000]];
    }

    /**
     * @dataProvider collideData
     */
    public function testCollide(array $A, array $B, bool $expected): void
    {
        $range = new Range($A[0], $A[1]);

        $result = $range->collide(new Range($B[0], $B[1]));

        self::assertSame($expected, $result);
    }

    public function collideData(): iterable
    {
        yield 'From is inside' => [[4, 8], [5, 10], true];
        yield 'To is inside' => [[4, 8], [2, 6], true];
        yield 'Is inside' => [[4, 8], [4, 7], true];
        yield 'Is outside' => [[4, 8], [2, 10], true];
        yield 'Edge on to' => [[4, 8], [8, 10], true];
        yield 'Edge on from' => [[4, 8], [1, 4], true];
        yield 'Not intersect on left' => [[4, 8], [1, 2], false];
        yield 'Not intersect on right' => [[4, 8], [9, 20], false];
    }

    /**
     * @dataProvider intersectData
     */
    public function testIntersect(array $A, array $B, array $expected): void
    {
        $range = new Range($A[0], $A[1]);

        $result = $range->intersect(new Range($B[0], $B[1]));

        self::assertEquals(new Range($expected[0], $expected[1]), $result);
    }

    public function intersectData(): iterable
    {
        yield 'On left' => [[2, 10], [8, 15], [8, 10]];
        yield 'On right' => [[2, 10], [-2, 5], [2, 5]];
        yield 'Inside' => [[2, 12], [4, 10], [4, 10]];
        yield 'Outside' => [[2, 12], [-1, 15], [2, 12]];
        yield 'The same' => [[3, 8], [3, 8], [3, 8]];
    }

    /**
     * @dataProvider adjacentData
     */
    public function testAdjacent(array $A, array $B, bool $expected): void
    {
        $range = new Range($A[0], $A[1]);

        $result = $range->adjacent(new Range($B[0], $B[1]));

        self::assertSame($expected, $result);
    }

    public function adjacentData(): iterable
    {
        yield 'On right' => [[4, 8], [9, 12], true];
        yield 'On left' => [[4, 8], [1, 3], true];
        yield 'Two point break on right' => [[4, 8], [10, 12], false];
        yield 'Two point break on left' => [[4, 8], [0, 2], false];
        yield 'Equals on right' => [[4, 8], [8, 12], true];
        yield 'Equals on left' => [[4, 8], [0, 4], true];
    }

    /**
     * @dataProvider diffData
     */
    public function testDiff(array $A, array $B, array $expected): void
    {
        $range = new Range($A[0], $A[1]);

        $result = $range->diff(new Range($B[0], $B[1]));

        $expectedResult = array_map(static fn (array $row) => new Range($row[0], $row[1]), $expected);

        self::assertEquals($expectedResult, $result);
    }

    public function diffData(): iterable
    {
        yield 'On right' => [[2, 10], [8, 15], [[2, 7]]];
        yield 'On left' => [[2, 10], [-2, 5], [[6, 10]]];

        yield 'Adjacent on right' => [[2, 10], [10, 12], [[2, 9]]];
        yield 'Adjacent on left' => [[2, 10], [0, 2], [[3, 10]]];

        yield 'Not collide on right' => [[2, 10], [11, 20], [[2, 10]]];
        yield 'Not collide on left' => [[2, 10], [-20, 1], [[2, 10]]];
        yield 'B is wider' => [[2, 5], [0, 10], []];
        yield 'B is wider, but adjacent' => [[2, 5], [1, 6], []];
        yield 'The same' => [[2, 5], [2, 5], []];
        yield 'The same on from' => [[2, 5], [2, 6], []];
        yield 'The same on to' => [[2, 5], [1, 5], []];

        yield 'Inside, both sides' => [[2, 9], [3, 8], [[2, 2], [9, 9]]];
        yield 'Inside, two spaces on both sides' => [[2, 9], [4, 7], [[2, 3], [8, 9]]];
        yield 'Inside, point' => [[2, 9], [5, 5], [[2, 4], [6, 9]]];
        yield 'Inside, adjacent on right' => [[2, 9], [5, 9], [[2, 4]]];
        yield 'Inside, adjacent on left' => [[2, 9], [2, 5], [[6, 9]]];
    }
}
