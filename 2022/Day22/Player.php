<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\DirectionalLocation;
use App\Utils\Location;
use App\Utils\Map;
use App\Utils\Rotation;

class Player
{
    private Location $position;
    private Direction $direction;
    public array $history = [];

    public function __construct(Location $startingPosition)
    {
        $this->position = $startingPosition;
        $this->direction = Direction::EAST;
    }

    public function rotate(Rotation $rotation): void
    {
        $this->direction = $this->direction->rotate($rotation);
        $this->addToHistory();
    }

    public function moveForward(int $maxSteps, Map $map): void
    {
        $step = 1;
        do {
            $newPosition = $this->moveOneStepInCurrentDirection();

            if ($this->canMoveHere($map, $newPosition)) {
                $this->position = $newPosition;
                $this->addToHistory();
                $step++;
            } elseif (
                $map->hasElementOnPoint($newPosition, ' ')
                || $map->isInsideMap($newPosition->y, $newPosition->x) === false
            ) {
                $newPosition = $this->wrapAroundMap($map);

                if ($newPosition === null) {
                    break;
                }

                $this->position = $newPosition;
                $this->addToHistory();
                $step++;
            } else {
                break;
            }
        } while ($step <= $maxSteps);
    }

    public function moveForwardOnCube(int $maxSteps, WalkableCube $walkableCube): void
    {
        $step = 1;
        do {
            $oldPosition = new DirectionalLocation($this->position, $this->direction);
            $newPosition = $walkableCube->getNextPosition($oldPosition);

            if ($oldPosition->equals($newPosition)) {
                break;
            }

            $this->position = $newPosition->location;
            $this->direction = $newPosition->direction;

            $this->addToHistory();
            $step++;
        } while ($step <= $maxSteps);
    }

    private function canMoveHere(Map $map, Location $newPosition): bool
    {
        return $map->hasElementOnPoint($newPosition, '#') === false
            && $map->hasElementOnPoint($newPosition, ' ') === false
            && $map->isPointInsideMap($newPosition);
    }

    public function getFinalPassword(): int
    {
        return ($this->position->y + 1) * 1_000 + ($this->position->x + 1) * 4 + $this->direction->value;
    }

    private function moveOneStepInCurrentDirection(): Location
    {
        return $this->position->moveInDirection($this->direction);
    }

    private function wrapAroundMap(Map $map): ?Location
    {
        if ($this->direction === Direction::EAST) {
            $block = $map->findFirstInRow($this->position->y, '#');
            $freeSpace = $map->findFirstInRow($this->position->y, '.');

            if ($block !== null && $block->isBeforeInRow($freeSpace)) {
                return null;
            }

            return $freeSpace;
        }

        if ($this->direction === Direction::WEST) {
            $block = $map->findLastInRow($this->position->y, '#');
            $freeSpace = $map->findLastInRow($this->position->y, '.');

            if ($block !== null && $block->isAfterInRow($freeSpace)) {
                return null;
            }

            return $freeSpace;
        }

        if ($this->direction === Direction::SOUTH) {
            $block = $map->findFirstInColumn($this->position->x, '#');
            $freeSpace = $map->findFirstInColumn($this->position->x, '.');

            if ($block !== null && $block->isBeforeInColumn($freeSpace)) {
                return null;
            }

            return $freeSpace;
        }

        if ($this->direction === Direction::NORTH) {
            $block = $map->findLastInColumn($this->position->x, '#');
            $freeSpace = $map->findLastInColumn($this->position->x, '.');

            if ($block !== null && $block->isAfterInColumn($freeSpace)) {
                return null;
            }

            return $freeSpace;
        }
    }

    private function addToHistory(): void
    {
        $this->history[] = [$this->position, $this->direction->asString()];
    }
}
