<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day13;

use AdventOfCode2022\Day13\Pair;
use PHPUnit\Framework\TestCase;

class PairTest extends TestCase
{
    /**
     * @dataProvider isInRightOrderData
     */
    public function testIsInRightOrder(string $input, bool $expected): void
    {
        $pair = Pair::parse($input);

        self::assertSame($expected, $pair->isRightOrder());

    }

    public function isInRightOrderData(): iterable
    {
        yield [
            <<<'TXT'
[1,1,3,1,1]
[1,1,5,1,1]
TXT,
            true
        ];

        yield [
            <<<'TXT'
[[1],[2,3,4]]
[[1],4]
TXT,
            true
        ];

        yield [
            <<<'TXT'
[9]
[[8,7,6]]
TXT,
            false
        ];


        yield [
            <<<'TXT'
[[4,4],4,4]
[[4,4],4,4,4]
TXT,
            true
        ];

        yield [
            <<<'TXT'
[7,7,7,7]
[7,7,7]
TXT,
            false
        ];

        yield [
            <<<'TXT'
[]
[3]
TXT,
            true
        ];

        yield [
            <<<'TXT'
[[[]]]
[[]]
TXT,
            false
        ];

        yield [
            <<<'TXT'
[1,[2,[3,[4,[5,6,7]]]],8,9]
[1,[2,[3,[4,[5,6,0]]]],8,9]
TXT,
            false
        ];
    }


}
