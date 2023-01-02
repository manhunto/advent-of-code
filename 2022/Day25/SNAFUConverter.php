<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day25;

use App\Utils\Collection;

class SNAFUConverter
{
    private const MINUS = '-';
    private const DOUBLE_MINUS = '=';
    private const BASE = 5;

    public function toSNAFU(string $decimal): string
    {
        $toFifthBase = base_convert($decimal, 10, self::BASE);

        $array = array_map('intval', str_split(strrev($toFifthBase)));

        $position = 0;

        do {
            $number = $array[$position];

            if (in_array($number, [3, 4, 5], true)) {
                if (isset($array[$position + 1])) {
                    ++$array[$position + 1];
                } else {
                    $array[$position + 1] = 1;
                }

                $array[$position] = match ($number) {
                    3 => '=',
                    4 => '-',
                    5 => 0
                };
            }

            $position++;
        } while ($position < count($array));

        return strrev(implode('', $array));
    }

    public function toDecimal(string $snafu): string
    {
        $new = [];

        foreach (array_reverse(str_split($snafu)) as $position => $item) {
            $factor = self::BASE ** $position;

            if ($item === self::DOUBLE_MINUS) {
                $number = -2;
            } elseif ($item === self::MINUS) {
                $number = -1;
            } else {
                $number = $item;
            }

            $new[$position] = $factor * $number;
        }

        return (string) Collection::create($new)
            ->reverse()
            ->sum();
    }
}
