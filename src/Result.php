<?php

declare(strict_types=1);

namespace App;

final class Result implements \Stringable
{
    public function __construct(
        public readonly mixed $partOne = null,
        public readonly mixed $partTwo = null,
    ) {
    }

    public static function fromArray(array $input): self
    {
        return new self($input[0] ?? null, $input[1] ?? null);
    }

    public function equals(Result $other): bool
    {
        return (string) $this->partOne === (string) $other->partOne && (string) $this->partTwo === (string) $other->partTwo;
    }

    public function __toString(): string
    {
        $output = '';

        if ($this->partOne) {
            $output = 'Part one: ' . $this->partOne;
        }

        if ($this->partTwo) {
            $output .= ' Part two: ' . $this->partTwo;
        }

        return $output;
    }
}
