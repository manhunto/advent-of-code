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

    public function hasAtLeasOneAnswer(): bool
    {
        return $this->isPartOneSolved() || $this->isPartTwoSolved();
    }

    public function isPartOneSolved(): bool
    {
        return $this->partOne !== null;
    }

    public function isPartTwoSolved(): bool
    {
        return $this->partTwo !== null;
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

    public function getAnswerForLevel(int $level): mixed
    {
        return match ($level) {
            1 => $this->partOne,
            2 => $this->partTwo,
            default => throw new \LogicException('Answer for level ' . $level . ' is not available')
        };
    }

    public function getAsArray(): array
    {
        return array_filter([$this->partOne, $this->partTwo]);
    }
}
