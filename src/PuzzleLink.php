<?php

declare(strict_types=1);

namespace App;

final class PuzzleLink implements \Stringable
{
    public function __construct(
        private readonly string $url,
    ) {
    }

    public static function fromDate(Date $date): PuzzleLink
    {
        $url = sprintf('https://adventofcode.com/%d/day/%d', $date->year, $date->getDayAsInt());

        return new self($url);
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
