<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Range;
use App\Utils\RangeCollection;
use PHPUnit\Framework\TestCase;

class RangeCollectionTest extends TestCase
{
    /**
     * @dataProvider unionData
     *
     * @param Range[] $rangesToAdd
     * @param Range[] $expectedRanges
     */
    public function testUnion(array $rangesToAdd, array $expectedRanges): void
    {
        $c = new RangeCollection();

        $c->union(...$rangesToAdd);

        self::assertEquals($expectedRanges, $c->getRanges());
    }

    public function unionData(): iterable
    {
        yield 'Adding one returns one' => [
            [
                new Range(2, 5)
            ],
            [
                new Range(2, 5)
            ]
        ];

        yield 'Adding two that intersects returns summed' => [
            [
                new Range(2, 5),
                new Range(4, 10)
            ],
            [
                new Range(2, 10)
            ]
        ];

        yield 'Adding three that interacts returns summed' => [
            [
                new Range(2, 6),
                new Range(5, 12),
                new Range(-2, 4),
            ],
            [
                new Range(-2, 12),
            ]
        ];

        yield 'Adding two that do not intersect returns both' => [
            [
                new Range(2, 5),
                new Range(7, 12)
            ],
            [
                new Range(2, 5),
                new Range(7, 12)
            ]
        ];

        yield 'First both do not intersect but last merge them to one' => [
            [
                new Range(2, 6),
                new Range(10, 20),
                new Range(4, 12),
            ],
            [
                new Range(2, 20)
            ]
        ];

        yield 'First both do not intersect but last two merge them to one' => [
            [
                new Range(2, 6),
                new Range(10, 20),
                new Range(4, 8),
                new Range(7, 15),
            ],
            [
                new Range(2, 20)
            ]
        ];

        yield 'First both do not intersect but last two adjacent merge them to one' => [
            [
                new Range(2, 3),
                new Range(10, 20),
                new Range(4, 6),
                new Range(7, 9),
            ],
            [
                new Range(2, 20)
            ]
        ];

        yield 'Three that do not intersect but last two adjacent merge them to one' => [
            [
                new Range(2, 3),
                new Range(10, 20),
                new Range(24, 50),
                new Range(4, 12),
                new Range(16, 23),
            ],
            [
                new Range(2, 50)
            ]
        ];
    }

    /**
     * @dataProvider intersectData
     *
     * @param Range[] $rangesToAdd
     * @param Range[] $expectedRanges
     */
    public function testIntersect(array $rangesToAdd, Range $intersect, array $expectedRanges): void
    {
        $c = new RangeCollection();

        $c->union(...$rangesToAdd);
        $c->intersect($intersect);

        self::assertEquals($expectedRanges, $c->getRanges());
    }

    public function intersectData(): iterable
    {
        yield 'Intersect with empty' => [
            [
            ],
            new Range(2, 5),
            [
            ]
        ];

        yield 'Intersect two' => [
            [
                new Range(2, 5),
            ],
            new Range(3, 6),
            [
                new Range(3, 5)
            ]
        ];

        yield 'Two ranges that does not intersect, and intersection crop them on sides' => [
            [
                new Range(-100, 105),
                new Range(107, 250)
            ],
            new Range(0, 200),
            [
                new Range(0, 105),
                new Range(107, 200)
            ]
        ];

        yield 'Wider range is cropped' => [
            [
                new Range(-100, 270),
            ],
            new Range(0, 200),
            [
                new Range(0, 200),
            ]
        ];
    }

    /**
     * @dataProvider sortedData
     */
    public function testGetRangesReturnsSorted(array $before, array $after): void
    {
        $collection = new RangeCollection();
        $collection->union(...$before);

        self::assertEquals($after, $collection->getRanges());
    }

    public function sortedData(): iterable
    {

        yield 'Empty' => [
            [
            ],
            [
            ]
        ];

        yield 'One item' => [
            [
                new Range(107, 200),
            ],
            [
                new Range(107, 200)
            ]
        ];

        yield 'Two items' => [
            [
                new Range(107, 200),
                new Range(0, 105),
            ],
            [
                new Range(0, 105),
                new Range(107, 200)
            ]
        ];

        yield 'More items' => [
            [
                new Range(107, 200),
                new Range(0, 105),
                new Range(247, 272),
                new Range(202, 245),
            ],
            [
                new Range(0, 105),
                new Range(107, 200),
                new Range(202, 245),
                new Range(247, 272),
            ]
        ];
    }

    /**
     * @dataProvider getGapsData
     */
    public function testGetGaps(array $before, array $after): void
    {
        $collection = new RangeCollection();
        $collection->union(...$before);

        self::assertEquals($after, $collection->getGaps());
    }

    public function getGapsData(): iterable
    {

        yield 'Empty' => [
            [
            ],
            [
            ]
        ];

        yield 'One item' => [
            [
                new Range(107, 200),
            ],
            [
            ]
        ];

        yield 'Two items' => [
            [
                new Range(107, 200),
                new Range(0, 105),
            ],
            [
                new Range(106, 106),
            ]
        ];

        yield 'More items' => [
            [
                new Range(109, 200),
                new Range(0, 105),
                new Range(267, 272),
                new Range(233, 245),
            ],
            [
                new Range(106, 108),
                new Range(201, 232),
                new Range(246, 266),
            ]
        ];
    }
}
