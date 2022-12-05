<?php

declare(strict_types=1);

namespace App;

final class Year implements \Stringable
{
    private function __construct(
        private readonly string $value,
    ) {
        if (!is_numeric($value) || strlen($value) !== 4) {
            throw new \LogicException('Invalid year format. It should be 4-digit numeric value. Given: ' . $value);
        }
    }

    public static function fromDateTime(\DateTimeImmutable $dateTime): self
    {
        return new Year($dateTime->format('Y'));
    }

    public static function fromString(string $year): self
    {
        return new self($year);
    }

    public function equals(Year $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
