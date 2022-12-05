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
            throw new \LogicException('Invalid day. Advent of Code span only from 1 to 25 day of december. Given: ' . $day);
        }

        if (!is_numeric($year) || strlen($year) !== 4) {
            throw new \LogicException('Invalid year. Given: ' . $year);
        }
    }

    public static function createForDateTime(\DateTimeImmutable $dateTime): Date
    {
        $year = $dateTime->format('Y');
        $day = $dateTime->format('d');

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
