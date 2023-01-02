<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day25;

use AdventOfCode2022\Day25\SNAFUConverter;
use PHPUnit\Framework\TestCase;

class SNAFUConverterTests extends TestCase
{
    private SNAFUConverter $sut;

    protected function setUp(): void
    {
        $this->sut = new SNAFUConverter();
    }

    /**
     * @dataProvider toSNAFUData
     */
    public function testToSNAFU(string $decimal, string $snafu): void
    {
        self::assertSame($snafu, $this->sut->toSNAFU($decimal));
    }

    public function toSNAFUData(): iterable
    {
        yield ['1', '1'];
        yield ['2', '2'];
        yield ['3', '1='];
        yield ['4', '1-'];
        yield ['5', '10'];
        yield ['6', '11'];
        yield ['7', '12'];
        yield ['8', '2='];
        yield ['9', '2-'];
        yield ['10', '20'];
        yield ['15', '1=0'];
        yield ['20', '1-0'];
        yield ['2022', '1=11-2'];
        yield ['12345', '1-0---0'];
        yield ['314159265', '1121-1110-1=0'];
    }

    /**
     * @dataProvider toDecimalData
     */
    public function testToDecimal(string $snafu, string $decimal): void
    {
        self::assertSame($decimal, $this->sut->toDecimal($snafu));
    }

    public function toDecimalData(): \Generator
    {
        yield ['1=-0-2', '1747'];
        yield ['12111', '906'];
        yield ['2=0=', '198'];
        yield ['21', '11'];
        yield ['2=01', '201'];
        yield ['111', '31'];
        yield ['20012', '1257'];
        yield ['112', '32'];
        yield ['1=-1=', '353'];
        yield ['1-12', '107'];
        yield ['12', '7'];
        yield ['1=', '3'];
        yield ['122', '37'];
    }
}
