<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day19;

use AdventOfCode2022\Day19\Factory;
use AdventOfCode2022\Day19\FactoryChecker;
use PHPUnit\Framework\TestCase;

class FactoryCheckerTest extends TestCase
{
    public function testFirstBlueprintFromExample(): void
    {
        $sut = new FactoryChecker();

        $costs = [
            'ore' => ['ore' => 4],
            'clay' => ['ore' => 2],
            'obsidian' => ['ore' => 3, 'clay' => 14],
            'geode' => ['ore' => 2, 'obsidian' => 7]
        ];

        $result = $sut->howMuchGeocodeCanProduce(new Factory($costs), 24);

        self::assertSame(9, $result);
    }
}
