<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Collection;
use App\Utils\DirectionalLocation;
use App\Utils\Location;
use App\Utils\Map;

class CubeEdgeMapper
{
    private readonly int $lengthOfEdge;

    public function __construct(
        private readonly Map $map,
        private readonly array $mapElements,
    ) {
        $this->lengthOfEdge = $this->calculateLengthOfEdge();
    }

    /**
     * @return Location[]
     */
    public function getEdgeInnerPoints(): array
    {
        $innerEdgePoint = [];

        /** @var Location $point */
        foreach ($this->map->getPointsWithElements($this->mapElements) as $point) {
            if ($this->isPointOnEdge($point) === false) {
                continue;
            }

            foreach ($point->getStraightAdjacent() as $adjacentPoint) {
                $element = $this->map->getElementForPoint($adjacentPoint);

                if (in_array($element, $this->mapElements, true) === false) {
                    continue 2;
                }
            }

            foreach ($point->getAdjacentOnDiagonals() as $adjacentOnDiagonal) {
                $element = $this->map->getElementForPoint($adjacentOnDiagonal);

                if (in_array($element, $this->mapElements, true) === false) {
                    $innerEdgePoint[] = $point;
                    break;
                }
            }
        }

        return $innerEdgePoint;
    }

    /**
     * @return Location[]
     */
    public function getOutsideEdgePoints(): array
    {
        $outsideEdgePoints = [];

        /** @var Location $point */
        foreach ($this->map->getPointsWithElements($this->mapElements) as $point) {
            if ($this->isOutsideEdge($point)) {
                $outsideEdgePoints = [...$outsideEdgePoints, ...$this->getDirectionsWithAbyss($point)];
            }
        }

        return $outsideEdgePoints;
    }

    private function calculateLengthOfEdge(): int
    {
        $area = $this->map->calculateAreaForElements($this->mapElements);

        return (int) sqrt($area / 6);
    }

    private function isPointOnEdge(Location $position): bool
    {
        return $position->y % $this->lengthOfEdge === 0
            || $position->y % $this->lengthOfEdge + 1 === 0
            || $position->x % $this->lengthOfEdge === 0
            || $position->x % $this->lengthOfEdge + 1 === 0;
    }

    private function isOutsideEdge(Location $point): bool
    {
        foreach ($point->getAllAdjacentPoints() as $adjacentPoint) {
            if ($this->map->isPointInsideMap($adjacentPoint) === false) {
                return true;
            }

            $element = $this->map->getElementForPoint($adjacentPoint);

            if (in_array($element, $this->mapElements, true) === false) {
                return true;
            }
        }

        return false;
    }

    public function getEdgeMap(): EdgeMapping
    {
        $edgeMap = new EdgeMapping();

        $outsidePoints = $this->getOutsideEdgePoints();

        foreach ($this->getEdgeInnerPoints() as $edgeInnerPoint) {
            $adjacent = $this->getAdjacentForPoints($edgeInnerPoint, $outsidePoints);

            if (count($adjacent) !== 2) {
                throw new \LogicException('Something went wrong in preparing edge map');
            }

            $prevEdgeDirA = null;
            $prevEdgeDirB = null;

            while (empty($adjacent) === false) {
                [$toA, $toB] = $adjacent;

                $edgeMap->add($toA, $toB);

                $outsidePoints = Collection::create($outsidePoints)
                    ->removeItems($adjacent)
                    ->toArray();

                $adjacentA = $this->getNextPoint($toA, $outsidePoints);
                $adjacentB = $this->getNextPoint($toB, $outsidePoints);

                if ($adjacentA === null || $adjacentB === null) {
                    break;
                }

                $nextEdgeDirA = $adjacentA->direction;
                $nextEdgeDirB = $adjacentB->direction;

                if ($prevEdgeDirA !== null && $prevEdgeDirB !== null && $prevEdgeDirA !== $nextEdgeDirA && $prevEdgeDirB !== $nextEdgeDirB) {
                    break;
                }

                $prevEdgeDirA = $nextEdgeDirA;
                $prevEdgeDirB = $nextEdgeDirB;

                $adjacent = [$adjacentA, $adjacentB];
            }
        }

        if (empty($outsidePoints) === false) {
            $pointsWithoutMapping = count($outsidePoints);
            $edgeWithoutMapping = ceil($pointsWithoutMapping / $this->lengthOfEdge);

            throw new \LogicException(sprintf('This shape of cube is not handled yet. Points without mapping %s. Missing edges %s', $pointsWithoutMapping, $edgeWithoutMapping));
        }

        return $edgeMap;
    }

    /**
     * @param DirectionalLocation[] $outsideEdgePoints
     * @return DirectionalLocation[]
     */
    private function getAdjacentForPoints(Location $point, array $outsideEdgePoints): array
    {
        return Collection::create($outsideEdgePoints)
            ->filter(static fn (DirectionalLocation $outside): bool => $point->isAdjacent($outside->location))
            ->values()
            ->toArray();
    }

    /**
     * @param DirectionalLocation[] $outsideEdgePoints
     */
    private function getNextPoint(DirectionalLocation $directionFromPoint, array $outsideEdgePoints): ?DirectionalLocation
    {
        $theSame = Collection::create($outsideEdgePoints)
            ->filter(static fn(DirectionalLocation $outside): bool => $directionFromPoint->hasTheSamePoint($outside))
            ->values();

        if ($theSame->isEmpty() === false) {
            return $theSame->first();
        }

        $adjacent = Collection::create($outsideEdgePoints)
            ->filter(static fn(DirectionalLocation $outside): bool => $directionFromPoint->isAdjacent($outside))
            ->values();

        if ($adjacent->count() === 1) {
            return $adjacent->first();
        }

        return $adjacent
            ->filter(static fn (DirectionalLocation $dirFromPoint): bool => $directionFromPoint->hasTheSameDirection($dirFromPoint))
            ->first();
    }

    /**
     * @return DirectionalLocation[]
     */
    private function getDirectionsWithAbyss(Location $point): array
    {
        return Collection::create(iterator_to_array($point->getStraightAdjacent()))
            ->filter(fn (Location $adjacent): bool =>
                $this->map->isPointInsideMap($adjacent) === false
                || in_array($this->map->getElementForPoint($adjacent), $this->mapElements, true) === false
            )
            ->forEach(static fn (Location $adjacent): DirectionalLocation => DirectionalLocation::fromLocationToLocation($point, $adjacent))
            ->values()
            ->toArray();
    }

    private function addMapping(DirectionalLocation $toA, DirectionalLocation $toB, array $edgeMap): array
    {
        $edgeMap[] = [$toA, $toB->reversedDirection()];
        $edgeMap[] = [$toB, $toA->reversedDirection()];

        return $edgeMap;
    }
}
