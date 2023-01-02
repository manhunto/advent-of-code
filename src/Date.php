<?php

declare(strict_types=1);

namespace App;

final class Date implements \Stringable
{
    public readonly string $day;
    public readonly Year $year;

    private function __construct(
        string $day,
        Year $year,
    ) {
        $this->day = str_pad($day, 2, '0', STR_PAD_LEFT);
        $this->year = $year;

        $dayAsInt = $this->getDayAsInt();

        if ($dayAsInt < 1 || $dayAsInt > 25) {
            throw new \LogicException('Invalid day. Advent of Code span only from 1 to 25 day of december. Given: ' . $day);
        }
    }

    public static function fromDateTime(\DateTimeImmutable $dateTime): Date
    {
        $day = $dateTime->format('d');

        return new self($day, Year::fromDateTime($dateTime));
    }

    public static function fromStrings(string $day, string $year): self
    {
        return new Date($day, Year::fromString($year));
    }

    public function withDay(string $day): self
    {
        return new self($day, $this->year);
    }

    public function withYear(string $year): self
    {
        return self::fromStrings($this->day, $year);
    }

    public function getDayAsInt(): int
    {
        return (int) ltrim($this->day, '0');
    }

    public function getYearAsString(): string
    {
        return (string) $this->year;
    }

    public function isYearEquals(Year $other): bool
    {
        return $this->year->equals($other);
    }

    public function __toString(): string
    {
        return sprintf('%s/%s', $this->day, $this->year);
    }
}
