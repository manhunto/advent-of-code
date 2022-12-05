<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day05;

final class Command
{
    private function __construct(
        public readonly int $quantity,
        public readonly int $fromStack,
        public readonly int $toStack,
    ) {
    }

    public static function fromString(string $command): self
    {
        preg_match('/move (.*) from (.*) to (.*)/', $command, $matches);

        [, $quantity, $from, $to] = $matches;

        return new self((int) $quantity, (int) $from, (int) $to);
    }
}
