<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

class Valve
{
    public function __construct(
        public readonly string $name,
        public readonly int $flowRate,
        public readonly array $neighbourValves,
    ) {
    }

    public function canValveBeOpen(): bool
    {
        return $this->flowRate > 0;
    }

    public function calculateReleasedPressure(int $minute): int
    {
        if ($this->canValveBeOpen() === false) {
            throw new \LogicException('Cannot open broken valve');
        }

        return $this->flowRate * $minute;
    }
}
