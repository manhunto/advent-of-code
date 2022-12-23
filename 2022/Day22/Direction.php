<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

enum Direction: int
{
    case RIGHT = 0;
    case DOWN = 1;
    case LEFT = 2;
    case UP = 3;

    private const COUNT = 4;

    public function asString(): string
    {
        return match ($this) {
            self::RIGHT => '>',
            self::DOWN => 'v',
            self::LEFT => '<',
            self::UP => '^',
        };
    }

    public function turnClockwise(): self
    {
        $next = ($this->value + 1) % self::COUNT;

        return self::from($next);
    }

    public function turnAntiClockwise(): self
    {
        $prev = ($this->value - 1) % self::COUNT;

        if ($prev < 0) {
            $prev = self::COUNT - 1;
        }

        return self::from($prev);
    }
}
