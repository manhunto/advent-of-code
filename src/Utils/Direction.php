<?php

declare(strict_types=1);

namespace App\Utils;

enum Direction: int
{
    case EAST = 0;
    case SOUTH = 1;
    case WEST = 2;
    case NORTH = 3;

    public function asString(): string
    {
        return match ($this) {
            self::EAST => '>',
            self::SOUTH => 'v',
            self::WEST => '<',
            self::NORTH => '^',
        };
    }

    public function turnClockwise(): self
    {
        $next = ($this->value + 1) % self::count();

        return self::from($next);
    }

    public function turnAntiClockwise(): self
    {
        $count = self::count();
        $prev = ($this->value - 1) % $count;

        if ($prev < 0) {
            $prev = $count - 1;
        }

        return self::from($prev);
    }

    public function reversed(): self
    {
        return $this->turnClockwise()->turnClockwise();
    }

    private static function count(): int
    {
        return count(self::cases());
    }
}
