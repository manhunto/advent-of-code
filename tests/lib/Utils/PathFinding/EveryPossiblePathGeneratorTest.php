<?php

declare(strict_types=1);

namespace Tests\Lib\Utils\PathFinding;

use App\Utils\PathFinding\EveryPossiblePathGenerator;
use App\Utils\PathFinding\Node;
use PHPUnit\Framework\TestCase;

class EveryPossiblePathGeneratorTest extends TestCase
{

    /**
     * @dataProvider generateData
     */
    public function testGenerate(array $nodesConfig, int $maxMoves, array $expected): void
    {
        $nodes = [];

        foreach ($nodesConfig as $nodeName => $neighbours) {
            $nodes[$nodeName] = new Node(
                $nodeName,
                $neighbours,
                visitableOnlyOnce: str_ends_with($nodeName, '-once'),
            );
        }

        $generator = new EveryPossiblePathGenerator($nodes, 'AA', $maxMoves);

        $paths = $generator->generate();

        self::assertEquals($expected, iterator_to_array($paths));
    }

    public function generateData(): iterable
    {
        yield [
            [
                'AA' => ['BB', 'CC'],
                'BB' => ['AA'],
                'CC' => ['AA']
            ],
            1,
            [
                ['AA', 'BB'],
                ['AA', 'CC']
            ]
        ];

        yield [
            [
                'AA' => ['BB', 'CC'],
                'BB' => ['DD', 'EE'],
                'CC' => ['FF', 'GG'],
                'DD' => ['BB'],
                'EE' => ['BB'],
                'FF' => ['CC'],
                'GG' => ['CC']
            ],
            2,
            [
                ['AA', 'BB', 'DD'],
                ['AA', 'BB', 'EE'],
                ['AA', 'CC', 'FF'],
                ['AA', 'CC', 'GG'],
            ]
        ];

        yield [
            [
                'AA' => ['BB', 'CC-once'],
                'BB' => ['AA', 'CC-once'],
                'CC-once' => ['AA', 'BB']
            ],
            3,
            [
            ]
        ];
    }

    public function testVisitedOnlyOnce(): void
    {
        $nodes = [
            new Node('AA', ['BB', 'CC-once']),
            new Node('BB', ['AA', 'CC-once']),
            new Node('CC-once', ['AA', 'BB'], true),
        ];

        $generator = new EveryPossiblePathGenerator($nodes, 'AA', 20);

        $paths = $generator->generate();

        self::assertNotEmpty($paths);

        foreach ($paths as $path) {
            self::assertCount(21, $path);

            $valuesCounted = array_count_values($path);
            self::assertLessThanOrEqual(1, $valuesCounted['CC-once'] ?? 0);
        }
    }
}
