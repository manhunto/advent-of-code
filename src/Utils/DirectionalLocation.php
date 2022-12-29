<?php

declare(strict_types=1);

namespace App\Utils;

class DirectionalLocation implements \Stringable
{
    public function __construct(
        public readonly Location $location,
        public readonly Direction $direction,
    ) {
    }

    private static function fromPrimitiveLocation(int $x, int $y, Direction $direction): self
    {
        return new self(new Location($x, $y), $direction);
    }

    public static function north(int $x, int $y): self
    {
        return self::fromPrimitiveLocation($x, $y, Direction::NORTH);
    }

    public static function west(int $x, int $y): self
    {
        return self::fromPrimitiveLocation($x, $y, Direction::WEST);
    }

    public static function east(int $x, int $y): self
    {
        return self::fromPrimitiveLocation($x, $y, Direction::EAST);
    }

    public static function south(int $x, int $y): self
    {
        return self::fromPrimitiveLocation($x, $y, Direction::SOUTH);
    }

    public static function fromLocationToLocation(Location $point, Location $other): self
    {
        return new self($point, $point->getDirection($other));
    }

    public function __toString(): string
    {
        return sprintf('%s->%s', $this->location, $this->direction->name);
    }

    public function reversedDirection(): self
    {
        return new self($this->location, $this->direction->reversed());
    }

    public function isAdjacent(DirectionalLocation $other): bool
    {
        return $this->location->isAdjacent($other->location);
    }

    public function hasTheSameDirection(DirectionalLocation $dirFromPoint): bool
    {
        return $this->direction === $dirFromPoint->direction;
    }

    public function hasTheSamePoint(DirectionalLocation $other): bool
    {
        return $this->location->equals($other->location);
    }

    public function hasTheSame(Location $point, Direction $direction): bool
    {
        return $this->location->equals($point) && $this->direction === $direction;
    }

    public function equals(DirectionalLocation $other): bool
    {
        return $this->hasTheSame($other->location, $other->direction);
    }
}
