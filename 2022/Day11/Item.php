<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day11;

class Item implements \Stringable
{
    public function __construct(
        private int $worryLevel
    ) {
    }

    public function pow(): void
    {
        $this->worryLevel = $this->worryLevel ** 2;
    }

    public function multiply(int $value): void
    {
        $this->worryLevel *= $value;
    }

    public function plus(int $value): void
    {
        $this->worryLevel += $value;
    }

    public function divideByThree(): void
    {
        $this->worryLevel = (int) floor($this->worryLevel / 3);
    }

    public function __toString(): string
    {
        return (string) $this->worryLevel;
    }

    public function isDivisibleBy(int $divisor): bool
    {
        return $this->worryLevel % $divisor === 0;
    }

    public function mod(int $value): void
    {
        $this->worryLevel %= $value;
    }
}
