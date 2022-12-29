<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Map;
use App\Utils\Location;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testFindingLastAndFirstInRowAndCompareTheirPositions(): void
    {
        $map = new Map([
            [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
            [' ', '.', '#', ' ', ' ', ' ', ' ', ' '],
            ['.', '#', '.', '.', '.', '#', ' ', ' '],
            [' ', '.', '.', '.', '#', '.', ' ', ' '],
            [' ', '.', '.', '.', '.', ' ', ' ', ' '],
            [' ', ' ', '#', '#', '.', ' ', ' ', ' '],
            [' ', ' ', '.', '.', ' ', ' ', ' ', ' '],
            [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
        ]);

        $block = $map->findFirstInRow(1, '#');
        $freeSpace = $map->findFirstInRow(1, '.');

        self::assertEquals(new Location(2, 1), $block);
        self::assertEquals(new Location(1, 1), $freeSpace);
        self::assertTrue($block->isOnEastTo($freeSpace));
        self::assertTrue($freeSpace->isOnWestTo($block));

        $block = $map->findFirstInColumn(2, '#');
        $freeSpace = $map->findFirstInColumn(2, '.');

        self::assertEquals(new Location(2, 1), $block);
        self::assertEquals(new Location(2, 2), $freeSpace);
        self::assertTrue($block->isOnNorthTo($freeSpace));
        self::assertTrue($freeSpace->isOnSouthTo($block));

        $block = $map->findLastInRow(2, '#');
        $freeSpace = $map->findLastInRow(2, '.');

        self::assertEquals(new Location(5, 2), $block);
        self::assertEquals(new Location(4, 2), $freeSpace);
        self::assertTrue($block->isOnEastTo($freeSpace));
        self::assertTrue($freeSpace->isOnWestTo($block));

        $block = $map->findLastInColumn(2, '#');
        $freeSpace = $map->findLastInColumn(2, '.');

        self::assertEquals(new Location(2, 5), $block);
        self::assertEquals(new Location(2, 6), $freeSpace);
        self::assertTrue($block->isOnNorthTo($freeSpace));
        self::assertTrue($freeSpace->isOnSouthTo($block));
    }
}
