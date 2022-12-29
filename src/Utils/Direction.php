<?php

declare(strict_types=1);

namespace App\Utils;

enum Direction: int
{
    case EAST = 0;
    case SOUTH = 1;
    case WEST = 2;
    case NORTH = 3;

    private const EAST_STRING = '>';
    private const SOUTH_STRING = 'v';
    private const WEST_STRING = '<';
    private const NORTH_STRING = '^';

    public static function tryFromString(string $item): self
    {
        return match ($item) {
            self::EAST_STRING => self::EAST,
            self::SOUTH_STRING => self::SOUTH,
            self::WEST_STRING => self::WEST,
            self::NORTH_STRING => self::NORTH,
            default => throw new \LogicException('Unexpected string direction ' . $item)
        };
    }

    public function asString(): string
    {
        return match ($this) {
            self::EAST => self::EAST_STRING,
            self::SOUTH => self::SOUTH_STRING,
            self::WEST => self::WEST_STRING,
            self::NORTH => self::NORTH_STRING,
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

    public function rotate(Rotation $rotation): self
    {
        return match ($rotation) {
            Rotation::CLOCKWISE => $this->turnClockwise(),
            Rotation::ANTICLOCKWISE => $this->turnAntiClockwise(),
            Rotation::TURNABOUT => $this->reversed(),
            default => throw new \LogicException('Unexpected rotation')
        };
    }

    private static function count(): int
    {
        return count(self::cases());
    }

    public function getRotationTo(self $other): Rotation
    {
        foreach (Rotation::cases() as $rotation) {
            $rotated = $this->rotate($rotation);

            if ($other === $rotated) {
                return $rotation;
            }
        }

        throw new \LogicException('Unexpected rotation state');
    }
}
