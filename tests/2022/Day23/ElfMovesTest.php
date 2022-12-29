<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day23;

use AdventOfCode2022\Day23\ElfMoves;
use App\Input;
use App\InputType;
use PHPUnit\Framework\TestCase;

class ElfMovesTest extends TestCase
{
    public function testName()
    {
        $string = <<<TXT
.....
..##.
..#..
.....
..##.
.....
TXT;
        $input = new Input($string, InputType::Example);

        $elfMoves = ElfMoves::fromInput($input);
        $result = $elfMoves->move();
        self::assertTrue($result);

        $result = $elfMoves->move();
        self::assertTrue($result);

        $result = $elfMoves->move();
        self::assertTrue($result);

        $result = $elfMoves->move();
        self::assertFalse($result);

//        $map = $elfMoves->generateMap();
//
//        $map->printer()
//            ->withoutRowNumbers()
//            ->naturalHorizontally()
//            ->print();
    }

}
