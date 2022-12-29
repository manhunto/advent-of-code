<?php

declare(strict_types=1);

namespace App\Utils;

class Location implements \Stringable
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }

    public function distanceInManhattanGeometry(self $other): int
    {
        return abs($this->x - $other->x) + abs($this->y - $other->y);
    }

    public function distanceInEuclideanGeometry(self $other): float
    {
        return sqrt(abs($this->x - $other->x) ** 2 + abs($this->y - $other->y) ** 2);
    }

    public function equals(self $other): bool
    {
        return $this->x === $other->x && $this->y === $other->y;
    }

    public function moveEast(): self
    {
        return $this->move(x: 1);
    }

    public function moveSouth(): self
    {
        return $this->move(y: 1);
    }

    public function moveWest(): self
    {
        return $this->move(x: -1);
    }

    public function moveNorth(): self
    {
        return $this->move(y: -1);
    }

    private function moveNorthEast(): self
    {
        return $this->move(x: 1, y: -1);
    }

    private function moveNorthWest(): self
    {
        return $this->move(x: -1, y: -1);
    }

    private function moveSouthEast(): self
    {
        return $this->move(x: 1, y: 1);
    }

    private function moveSouthWest(): self
    {
        return $this->move(x: -1, y: 1);
    }

    public function isOnNorthTo(self $than): bool
    {
        $this->validateTheSameColumn($than);

        return $this->y < $than->y;
    }

    public function isOnSouthTo(self $than): bool
    {
        $this->validateTheSameColumn($than);

        return $this->y > $than->y;
    }

    public function isOnWestTo(self $than): bool
    {
        $this->validateTheSameRow($than);

        return $this->x < $than->x;
    }

    public function isOnEastTo(self $than): bool
    {
        $this->validateTheSameRow($than);

        return $this->x > $than->x;
    }

    private function validateTheSameRow(self $than): void
    {
        if ($this->isTheSameRow($than) === false) {
            throw new \LogicException('Point are not in the same row');
        }
    }

    private function isTheSameRow(self $than): bool
    {
        return $this->y === $than->y;
    }

    private function validateTheSameColumn(self $than): void
    {
        if ($this->isTheSameColumn($than) === false) {
            throw new \LogicException('Point are not in the same column');
        }
    }

    private function isTheSameColumn(self $than): bool
    {
        return $this->x === $than->x;
    }

    public function moveInDirection(Direction $direction): self
    {
        return match ($direction) {
            Direction::EAST => $this->moveEast(),
            Direction::SOUTH => $this->moveSouth(),
            Direction::WEST => $this->moveWest(),
            Direction::NORTH => $this->moveNorth(),
        };
    }

    /**
     * @return iterable<self>
     */
    public function getStraightAdjacent(): iterable
    {
        foreach (Direction::cases() as $direction) {
            yield $this->moveInDirection($direction);
        }
    }

    /**
     * @return iterable<self>
     */
    public function getAdjacentOnDiagonals(): iterable
    {
        $diagonals = [
            [1, 1],
            [-1, -1],
            [1, -1],
            [-1, 1]
        ];

        foreach ($diagonals as $diagonal) {
            yield $this->move($diagonal[0], $diagonal[1]);
        }
    }

    /**
     * @return iterable<self>
     */
    public function getAllAdjacentLocations(): iterable
    {
        yield from [
            ...$this->getStraightAdjacent(),
            ...$this->getAdjacentOnDiagonals()
        ];
    }

    private function move(int $x = 0, int $y = 0): self
    {
        return new self($this->x + $x, $this->y + $y);
    }

    public function isAdjacent(self $other): bool
    {
        foreach ($this->getStraightAdjacent() as $adjacent) {
            if ($other->equals($adjacent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Array based coordinate system
     */
    public function getDirection(self $other): Direction
    {
        if ($this->isTheSameRow($other)) {
            if ($this->isOnWestTo($other)) {
                return Direction::EAST;
            }

            return Direction::WEST;
        }

        if ($this->isTheSameColumn($other)) {
            if ($this->isOnNorthTo($other)) {
                return Direction::SOUTH;
            }

            return Direction::NORTH;
        }

        throw new \LogicException('Cannot get direction for points that are not in the same row or column');
    }

    public function __toString(): string
    {
        return sprintf('%d,%d', $this->x, $this->y);
    }

    /**
     * Example if North is given then N, NE and NW is returned.
     * Always three adjacent locations for every direction.
     *
     * @return self[]
     */
    public function getAllAdjacentLocationsInDirection(Direction $direction): array
    {
        return match ($direction) {
            Direction::NORTH => [
                $this->moveNorth(), $this->moveNorthEast(), $this->moveNorthWest(),
            ],
            Direction::SOUTH => [
                $this->moveSouth(), $this->moveSouthEast(), $this->moveSouthWest(),
            ],
            Direction::EAST => [
                $this->moveEast(), $this->moveNorthEast(), $this->moveSouthEast(),
            ],
            Direction::WEST => [
                $this->moveWest(), $this->moveNorthWest(), $this->moveSouthWest(),
            ],
            default => throw new \LogicException('Direction unhandled')
        };
    }
}
