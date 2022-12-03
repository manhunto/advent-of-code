<?php

declare(strict_types=1);

namespace App;

class SolverFullyQualifiedClassname
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
        return new self(sprintf("AdventOfCode%s\Day%s\Solution", $date->year, $date->day));
    }

    public function getDate(): Date
    {
        preg_match(self::REGEX, $this->fqn, $matches);

        return new Date($matches[1], $matches[2]);
    }
}
