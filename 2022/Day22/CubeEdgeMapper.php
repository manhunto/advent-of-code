<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Utils\Collection;
use App\Utils\Direction;
use App\Utils\Map;
use App\Utils\Point;

class CubeEdgeMapper
{
    use CubeTrait;

    private readonly int $lengthOfEdge;

    public function __construct(
        private readonly Map $map,
        private readonly array $mapElements,
    ) {
        $this->lengthOfEdge = $this->calculateLengthOfEdge();
    }

    /**
     * @return Point[]
     */
    public function getEdgeInnerPoints(): array
    {
        $innerEdgePoint = [];

        /** @var Point $point */
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
     * @return Point[]
     */
    public function getOutsideEdgePoints(): array
    {
        $outsideEdgePoints = [];

        /** @var Point $point */
        foreach ($this->map->getPointsWithElements($this->mapElements) as $point) {
            if ($this->isOutsideEdge($point)) {
                $outsideEdgePoints = [...$outsideEdgePoints, ...$this->getDirectionsWithAbyss($point)];
            }
        }

        return $outsideEdgePoints;
    }

    private function isPointOnEdge(Point $position)
    {
        return $position->y % $this->lengthOfEdge === 0
            || $position->y % $this->lengthOfEdge + 1 === 0
            || $position->x % $this->lengthOfEdge === 0
            || $position->x % $this->lengthOfEdge + 1 === 0;
    }

    private function isOutsideEdge(Point $point): bool
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
            throw new \LogicException('This shape of cube is not handled yet');
        }

        return $edgeMap;
    }

    /**
     * @param DirectionFromPoint[] $outsideEdgePoints
     * @return DirectionFromPoint[]
     */
    private function getAdjacentForPoints(Point $point, array $outsideEdgePoints): array
    {
        return Collection::create($outsideEdgePoints)
            ->filter(static fn (DirectionFromPoint $outside): bool => $point->isAdjacent($outside->point))
            ->values()
            ->toArray();
    }

    /**
     * @param DirectionFromPoint[] $outsideEdgePoints
     */
    private function getNextPoint(DirectionFromPoint $directionFromPoint, array $outsideEdgePoints): ?DirectionFromPoint
    {
        $theSame = Collection::create($outsideEdgePoints)
            ->filter(static fn(DirectionFromPoint $outside): bool => $directionFromPoint->hasTheSamePoint($outside))
            ->values();

        if ($theSame->isEmpty() === false) {
            return $theSame->first();
        }

        $adjacent = Collection::create($outsideEdgePoints)
            ->filter(static fn(DirectionFromPoint $outside): bool => $directionFromPoint->isAdjacent($outside))
            ->values();

        if ($adjacent->count() === 1) {
            return $adjacent->first();
        }

        return $adjacent
            ->filter(static fn (DirectionFromPoint $dirFromPoint): bool => $directionFromPoint->hasTheSameDirection($dirFromPoint))
            ->first();
    }

    /**
     * @return DirectionFromPoint[]
     */
    private function getDirectionsWithAbyss(Point $point): array
    {
        return Collection::create(iterator_to_array($point->getStraightAdjacent()))
            ->filter(fn (Point $adjacent): bool =>
                $this->map->isPointInsideMap($adjacent) === false
                || in_array($this->map->getElementForPoint($adjacent), $this->mapElements, true) === false
            )
            ->forEach(static fn (Point $adjacent): DirectionFromPoint => DirectionFromPoint::fromPointToPoint($point, $adjacent))
            ->values()
            ->toArray();
    }

    private function addMapping(DirectionFromPoint $toA, DirectionFromPoint $toB, array $edgeMap): array
    {
        $edgeMap[] = [$toA, $toB->reversed()];
        $edgeMap[] = [$toB, $toA->reversed()];

        return $edgeMap;
    }
}
