<?php

declare(strict_types=1);

namespace App;

final class Date
{
    public string $day;
    public string $year;

    public function __construct(
        string $day,
        string $year,
    ) {
        $this->day = str_pad($day, 2, '0', STR_PAD_LEFT);
        $this->year = $year;

        $dayAsInt = $this->getDayAsInt();

        if ($dayAsInt < 1 || $dayAsInt > 25) {
            throw new \LogicException('Invalid day. Advent of Code span only from 1 to 25 day. Given: ' . $day);
        }
    }

    public static function createForToday(): self
    {
        $today = new \DateTimeImmutable();
        $year = $today->format('Y');
        $day = $today->format('d');

        return new self($day, $year);
    }

    public function withDay(string $day): self
    {
        return new self($day, $this->year);
    }

    public function withYear(string $year): self
    {
        return new self($this->day, $year);
    }

    public function getDayAsInt(): int
    {
        return (int) ltrim($this->day, '0');
    }
}
