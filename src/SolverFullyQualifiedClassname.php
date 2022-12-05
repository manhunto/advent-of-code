<?php

declare(strict_types=1);

namespace App;

final class SolverFullyQualifiedClassname
{
    private const REGEX = '/AdventOfCode(\d{4})\\\Day(\d{2})\\\Solution/';
    private string $fqn;

    private function __construct(
        string $fqn,
    ) {
        if (!preg_match(self::REGEX, $fqn)) {
            throw new \LogicException('Invalid fqn for solution. Given ' . $fqn);
        }

        $this->fqn = $fqn;
    }

    public static function fromObject(Solver $solver): self
    {
        return new self(get_class($solver));
    }

    public static function fromDate(Date $date): self
    {
        return new self(sprintf("AdventOfCode%s\Day%s\Solution", $date->getYearAsString(), $date->day));
    }

    public function getDate(): Date
    {
        preg_match(self::REGEX, $this->fqn, $matches);

        return Date::fromStrings($matches[2], $matches[1]);
    }

    public function getAsString(): string
    {
        return $this->fqn;
    }
}
