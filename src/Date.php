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
}
