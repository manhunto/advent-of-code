<?php

declare(strict_types=1);

namespace App;

final class Result implements \Stringable
{
    public function __construct(
        private readonly mixed $partOne,
        private readonly mixed $partTwo = null,
    ) {
    }

    public static function fromArray(array $input): self
    {
        return new self($input[0], $input[1] ?? null);
    }

    public function equals(Result $other): bool
    {
        return (string) $this->partOne === (string) $other->partOne && (string) $this->partTwo === (string) $other->partTwo;
    }

    public function __toString(): string
    {
        $output = 'Part one: ' . $this->partOne;

        if ($this->partTwo) {
            $output .= PHP_EOL . 'Part two: ' . $this->partTwo;
        }

        return $output;
    }
}
