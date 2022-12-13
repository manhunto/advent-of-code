<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day13;

class Pair
{
    private function __construct(
        private readonly Packet $up,
        private readonly Packet $down,
    ) {
    }

    public static function parse(string $input): self
    {
        [$upInput, $downInput] = explode(PHP_EOL, $input);

        $up = Packet::parse($upInput);
        $down = Packet::parse($downInput);

        return new self($up, $down);
    }

    public function isRightOrder(): bool
    {
        return $this->up->isLowerThan($this->down);
    }
}
