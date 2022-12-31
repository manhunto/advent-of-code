<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day19;

use AdventOfCode2022\Day19\Factory;
use AdventOfCode2022\Day19\FactoryChecker;
use PHPUnit\Framework\TestCase;

class FactoryCheckerTest extends TestCase
{
    public function testName()
    {
        $sut = new FactoryChecker();

        $costs = [
            'ore' => ['ore' => 4],
            'clay' => ['ore' => 2],
            'obsidian' => ['ore' => 3, 'clay' => 14],
            'geode' => ['ore' => 2, 'obsidian' => 7]
        ];

//        $costs = [
//            'ore' => ['ore' => 1],
//            'clay' => ['ore' => 1],
//            'obsidian' => ['ore' => 1, 'clay' => 1],
//            'geode' => ['ore' => 1, 'obsidian' => 1]
//        ];


        $factory = new Factory($costs);

        $test = $sut->howMuchGeocodeCanProduce($factory, 24);

        self::assertSame(10, $test);

    }

}
