<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day25;

use App\Utils\Collection;
use App\Utils\Strings;

class SNAFUConverter
{
    private const MINUS = '-';
    private const DOUBLE_MINUS = '=';
    private const BASE = 5;

    public function toSNAFU(string $decimal): string
    {
        $toFifthBase = base_convert($decimal, 10, self::BASE);
        $fromRightToLeft = Strings::create($toFifthBase)
            ->reverse()
            ->toChars()
            ->eachToInt()
            ->toArray();

        $position = 0;

        do {
            $number = $fromRightToLeft[$position];

            if ($number > 2) {
                $fromRightToLeft[$position + 1] ??= 0;
                ++$fromRightToLeft[$position + 1];
            }

            $fromRightToLeft[$position] = $this->numberToSNAFU($number);

            $position++;
        } while ($position < count($fromRightToLeft));

        return (string) Collection::create($fromRightToLeft)
            ->implode('')
            ->reverse();
    }

    public function toDecimal(string $snafu): string
    {
        $decimal = [];

        foreach (array_reverse(str_split($snafu)) as $position => $item) {
            $factor = self::BASE ** $position;
            $decimal[$position] = $factor * $this->SNAFUCharToNumber($item);
        }

        return (string) Collection::create($decimal)
            ->reverse()
            ->sum();
    }

    private function SNAFUCharToNumber(string $item): int
    {
        if (preg_match('/^[0-2=-]$/', $item) === false) {
            throw new \LogicException('Invalid SNAFU character: ' . $item);
        }

        return match ($item) {
            self::DOUBLE_MINUS => -2,
            self::MINUS => -1,
            default => (int) $item
        };
    }

    private function numberToSNAFU(int $number): string
    {
        return match ($number) {
            0, 1, 2 => (string) $number,
            3 => self::DOUBLE_MINUS,
            4 => self::MINUS,
            5 => '0',
            default => throw new \LogicException('Wrong number: ' . $number)
        };
    }
}
