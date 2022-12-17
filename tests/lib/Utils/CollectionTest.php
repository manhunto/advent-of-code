<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @dataProvider removeAtBeginningData
     */
    public function testRemoveAtBeginning(int $howMuch, array $expected): void
    {
        $c = new Collection(['A', 'B', 'C', 'D']);
        $r = $c->removeAtBeginning($howMuch);

        self::assertEquals($expected, $r->toArray());
    }

    public function removeAtBeginningData(): iterable
    {
        yield [0, ['A', 'B', 'C', 'D']];
        yield [1, ['B', 'C', 'D']];
        yield [2, ['C', 'D']];
        yield [3, ['D']];
        yield [4, []];
    }
}
