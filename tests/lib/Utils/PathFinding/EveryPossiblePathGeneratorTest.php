<?php

declare(strict_types=1);

namespace Tests\Lib\Utils\PathFinding;

use App\Utils\PathFinding\EveryPossiblePathGenerator;
use App\Utils\PathFinding\Node;
use PHPUnit\Framework\TestCase;

class EveryPossiblePathGeneratorTest extends TestCase
{
    public function testAllVisibleOnce(): void
    {
        $nodes = [
            new Node('AA', ['BB', 'CC']),
            new Node('BB', ['AA', 'CC']),
            new Node('CC', ['AA', 'BB']),
        ];

        $generator = new EveryPossiblePathGenerator($nodes, 'AA');

        $paths = $generator->generate();

        self::assertEquals([
            ['AA', 'BB', 'CC'],
            ['AA', 'CC', 'BB']
        ], $paths);
    }
}
