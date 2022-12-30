<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Direction;
use App\Utils\DirectionalLocation;
use App\Utils\Location;
use App\Utils\Map;

class WalkableCube
{
    private readonly int $lengthOfEdge;

    public function __construct(
        private readonly array $walkableElements,
        private readonly Map $map,
        private readonly array $mapElements,
        private readonly EdgeMapping $edgeMapping,
    ) {
    }

    public function getNextPosition(DirectionalLocation $directionalPoint): DirectionalLocation
    {
        $position = $directionalPoint->location;
        $direction = $directionalPoint->direction;

        $this->validateCurrentPosition($position);

        $nextPosition = $position->moveInDirection($direction);

        if ($this->isMapElement($nextPosition)) {
            if ($this->isPointWalkable($nextPosition)) {
                return new DirectionalLocation($nextPosition, $direction);
            }

            return $directionalPoint;
        }

        return $this->handleEdgeMove($position, $direction);
    }

    private function validateCurrentPosition(Location $position): void
    {
        foreach ($this->walkableElements as $walkableElement) {
            if ($this->map->hasElement($position->y, $position->x, $walkableElement) === false) {
                throw new \LogicException('Current position is no walkable ' . $position);
            }
        }

        if ($this->map->isPointInsideMap($position) === false) {
            throw new \LogicException('Current position is not inside map');
        }
    }

    private function handleEdgeMove(Location $position, Direction $direction): DirectionalLocation
    {
        $next = $this->edgeMapping->getFor($position, $direction);

        if ($this->isPointWalkable($next->location)) {
            return $next;
        }

        return new DirectionalLocation($position, $direction);
    }

    private function isPointWalkable(Location $point): bool
    {
        foreach ($this->walkableElements as $walkableElement) {
            if ($this->map->hasElementOnPoint($point, $walkableElement) === false) {
                return false;
            }
        }

        return true;
    }

    private function isMapElement(Location $position): bool
    {
        $item = $this->map->getElementForLocation($position);

        return in_array($item, $this->mapElements, true);
    }
}
