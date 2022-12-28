<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\Point;

class DirectionFromPoint implements \Stringable
{
    public function __construct(
        public readonly Point $point,
        public readonly Direction $direction,
    ) {
    }

    private static function fromPrimitivePoint(int $x, int $y, Direction $direction): self
    {
        return new self(new Point($x, $y), $direction);
    }

    public static function north(int $x, int $y): self
    {
        return self::fromPrimitivePoint($x, $y, Direction::NORTH);
    }

    public static function west(int $x, int $y): self
    {
        return self::fromPrimitivePoint($x, $y, Direction::WEST);
    }

    public static function east(int $x, int $y): self
    {
        return self::fromPrimitivePoint($x, $y, Direction::EAST);
    }

    public static function south(int $x, int $y): self
    {
        return self::fromPrimitivePoint($x, $y, Direction::SOUTH);
    }

    public static function fromPointToPoint(Point $point, Point $other): self
    {
        return new self($point, $point->getDirection($other));
    }

    public function __toString(): string
    {
        return sprintf('%s->%s', $this->point, $this->direction->name);
    }

    public function reversed(): self
    {
        return new self($this->point, $this->direction->reversed());
    }

    public function isAdjacent(DirectionFromPoint $other): bool
    {
        return $this->point->isAdjacent($other->point);
    }

    public function hasTheSameDirection(DirectionFromPoint $dirFromPoint): bool
    {
        return $this->direction === $dirFromPoint->direction;
    }

    public function hasTheSamePoint(DirectionFromPoint $other): bool
    {
        return $this->point->equals($other->point);
    }

    public function hasTheSame(Point $point, Direction $direction): bool
    {
        return $this->hasTheSamePoint($point) && $this->hasTheSameDirection($direction);
    }
}
