<?php

declare(strict_types=1);

namespace App\Utils;

final class Range implements \Stringable
{
    public function __construct(
        public readonly int $from,
        public readonly int $to
    ) {
        if ($this->from > $this->to) {
            throw new \LogicException(sprintf('From cannot be greater than to in range. Given {%d, %d}.', $this->from, $this->to));
        }
    }

    public static function createForPoint(int $x): self
    {
        return new self($x, $x);
    }

    /**
     * All numbers are in A, in B or in both A and B
     *
     * @throws \LogicException
     */
    public function union(self $other): self
    {
        if ($this->collide($other) === false && $this->adjacent($other) === false) {
            throw new \LogicException('Cannot union not colliding or adjacent ranges');
        }

        $from = min($this->from, $other->from);
        $to = max($this->to, $other->to);

        return new Range($from, $to);
    }

    /**
     * All numbers which are in both A and B
     *
     * @throws \LogicException
     */
    public function intersect(self $other): self
    {
        if ($this->collide($other) === false) {
            throw new \LogicException('Cannot intersect not colliding ranges');
        }

        $from = max($this->from, $other->from);
        $to = min($this->to, $other->to);

        return new self($from, $to);
    }

    /**
     * All numbers which are in A, but not in B
     *
     * @param Range $other
     * @return Range[]
     */
    public function diff(self $other): array
    {
        // B is outside A
        if ($other->from <= $this->from && $other->to >= $this->to) {
            return [];
        }

        // B is inside A
        if ($other->from > $this->from && $other->to < $this->to) {
            return [
                new self($this->from, $other->from - 1),
                new self($other->to + 1, $this->to),
            ];
        }

        // Collide on side
        if ($this->from < $other->from) {
            $from = min($this->from, $other->from);
            $to = max($this->from, $other->from) - 1;
        } else {
            $from = min($this->to, $other->to) + 1;
            $to = max($this->to, $other->to);
        }

        return [
            new self($from, $to)
        ];
    }

    public function collide(self $other): bool
    {
        return $this->isNumberInRange($other->from)
            || $this->isNumberInRange($other->to)
            || $other->isNumberInRange($this->from)
            || $other->isNumberInRange($this->to);
    }

    public function adjacent(self $range): bool
    {
        return abs($this->to - $range->from) <= 1 || abs($this->from - $range->to) <= 1;
    }

    public function isPoint(): bool
    {
        return $this->length() === 1;
    }

    /**
     * @return int[]
     */
    public function getItems(): iterable
    {
        for ($i = $this->from ; $i <= $this->to; $i++) {
            yield $i;
        }
    }

    public function length(): int
    {
        return $this->to - $this->from + 1;
    }

    public function __toString(): string
    {
        return json_encode(['from' => $this->from, 'to' => $this->to], JSON_THROW_ON_ERROR);
    }

    public function expandTo(int $value): self
    {
        return new self(
            min($this->from, $value),
            max($this->to, $value),
        );
    }

    public function expandBoundariesBy(int $value): self
    {
        return new self(
            $this->from - $value,
            $this->to + $value
        );
    }

    public function isNumberInRange(int $number): bool
    {
        return $this->from <= $number && $this->to >= $number;
    }

    public function isBefore(int $number): bool
    {
        return $number < $this->from;
    }

    public function isAfter(int $number): bool
    {
        return $number > $this->to;
    }
}
