<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Map;
use App\Utils\Point;

class Player
{
    private Point $position;
    private Direction $direction;
    public array $history = [];

    public function __construct(Point $startingPosition)
    {
        $this->position = $startingPosition;
        $this->direction = Direction::RIGHT;
    }

//    public function getPosition(): Point
//    {
//        return $this->position;
//    }
//
//    public function getDirection(): Direction
//    {
//        return $this->direction;
//    }

    public function turnClockwise(): void
    {
        $this->direction = $this->direction->turnClockwise();
        $this->addToHistory();
    }

    public function turnAntiClockwise(): void
    {
        $this->direction = $this->direction->turnAntiClockwise();
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
                $map->hasElement($newPosition->y, $newPosition->x, ' ')
                || $map->isInsideMap($newPosition->y, $newPosition->x) === false
            ) {
                $newPosition = $this->wrapAroundMap($map);

//                if ($newPosition !== null && $map->hasElement($newPosition->y, $newPosition->x, ' ')) {
//                    var_dump('test'); // todo check me..
//                }

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

    private function canMoveHere(Map $map, Point $newPosition): bool
    {
        $x = $newPosition->x;
        $y = $newPosition->y;

        return $map->hasElement($y, $x, '#') === false
            && $map->hasElement($y, $x, ' ') === false
            && $map->isInsideMap($y, $x);
    }

    public function getFinalPassword(): int
    {
        return ($this->position->y + 1) * 1_000 + ($this->position->x + 1) * 4 + $this->direction->value;
    }

    private function drawCurrent(Map $map): void
    {
        $map->drawPoint($this->position, $this->direction->asString());
    }

    private function moveOneStepInCurrentDirection(): Point
    {
        return match ($this->direction) {
            Direction::RIGHT => $this->position->moveRight(),
            Direction::DOWN => $this->position->moveDown(),
            Direction::LEFT => $this->position->moveLeft(),
            Direction::UP => $this->position->moveUp(),
        };
    }

    private function wrapAroundMap(Map $map): ?Point
    {
        if ($this->direction === Direction::RIGHT) {
            $block = $map->findFirstInRow($this->position->y, '#');
            $freeSpace = $map->findFirstInRow($this->position->y, '.');

            if ($block !== null && $block->isBeforeInRow($freeSpace)) {
                return null;
            }

            return $freeSpace;
        }

        if ($this->direction === Direction::LEFT) {
            $block = $map->findLastInRow($this->position->y, '#');
            $freeSpace = $map->findLastInRow($this->position->y, '.');

            if ($block !== null && $block->isAfterInRow($freeSpace)) {
                return null;
            }

            return $freeSpace;
        }

        if ($this->direction === Direction::DOWN) {
            $block = $map->findFirstInColumn($this->position->x, '#');
            $freeSpace = $map->findFirstInColumn($this->position->x, '.');

            if ($block !== null && $block->isBeforeInColumn($freeSpace)) {
                return null;
            }


            return $freeSpace;
        }

        if ($this->direction === Direction::UP) {
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
