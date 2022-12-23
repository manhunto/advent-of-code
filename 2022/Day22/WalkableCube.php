<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\Map;
use App\Utils\Point;

class WalkableCube
{
    public function __construct(
        private readonly CubeTemplate $template,
        private readonly int          $sizeOfSideEdge,
        private readonly array        $walkableElements,
        private readonly Map          $map
    ) {
    }

    public function getNextPosition(Point $position, Direction $direction): Point
    {
        $this->validateCurrentPosition($position);

        $nextPosition = $position->moveInDirection($direction);

        if ($this->canMoveThroughMap($nextPosition)) {
            return $nextPosition;
        }

        if ($this->isPointOnEdge($position) === false) {
            return $position->moveInDirection($direction);
        }

        return $this->handleEdgeMove($position, $direction);

    }

    private function validateCurrentPosition(Point $position): void
    {
        foreach ($this->walkableElements as $walkableElement) {
            if ($this->map->hasElement($position->y, $position->x, $walkableElement) === false) {
                throw new \LogicException('Current position is no walkable');
            }
        }

        if ($this->map->isPointInsideMap($position) === false) {
            throw new \LogicException('Current position is not inside map');
        }
    }

    private function isPointOnEdge(Point $position): bool
    {
        return $position->y % $this->sizeOfSideEdge === 0
            || $position->y % $this->sizeOfSideEdge + 1 === 0
            || $position->x % $this->sizeOfSideEdge === 0
            || $position->x % $this->sizeOfSideEdge + 1 === 0;
    }

    private function canMoveThroughMap(Point $nextPosition): bool
    {
        if ($this->map->isPointInsideMap($nextPosition) === false) {
            return false;
        }

        foreach ($this->walkableElements as $walkableElement) {
            if ($this->map->hasElementOnPoint($nextPosition, $walkableElement) === false) {
                return false;
            }
        }

        return true;
    }

    private function handleEdgeMove(Point $position, Direction $direction): Point
    {
        // https://stackoverflow.com/a/66179230/12540449

        if ($this->template === CubeTemplate::EXAMPLE_INPUT) {
            $currentSide = $this->template->getCurrentFace($position, $this->sizeOfSideEdge);
            $nextFace = $this->template->getFaceInDirection();


            var_dump($currentSide);
        }

        throw new \LogicException('Unhandled move');
    }
}
