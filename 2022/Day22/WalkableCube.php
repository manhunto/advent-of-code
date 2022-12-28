<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\Map;
use App\Utils\Point;

/**
 * @todo https://www.youtube.com/watch?v=qWgLdNFYDDo
 */
class WalkableCube
{
    use CubeTrait;

    private readonly int $lengthOfEdge;

    public function __construct(
        private readonly array $walkableElements,
        private readonly Map $map,
        private readonly array $mapElements
    ) {
        $this->lengthOfEdge = $this->calculateLengthOfEdge();
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
        return $position->y % $this->lengthOfEdge === 0
            || $position->y % $this->lengthOfEdge + 1 === 0
            || $position->x % $this->lengthOfEdge === 0
            || $position->x % $this->lengthOfEdge + 1 === 0;
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
        $innerPoints = $this->getInnerPoints(); // todo extract to edge mapper

        // todo walk on edges and create map point1 to point2 and dir with rotation
        // todo walk each po

        var_dump($innerPoints); die;



        throw new \LogicException('Unhandled move');
    }
}
