<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day18;

use App\Utils\FloodFill;
use App\Utils\Point3D;
use App\Utils\Space3DBoundary;

class Droplet
{
    /** @var Point3D[] */
    private array $points;

    /** @param Point3D[] $points */
    public function __construct(
        array $points
    ) {
        foreach ($points as $point) {
            $this->points[(string) $point] = $point;
        }
    }

    public function calculateWholeSurface(): int
    {
        $surface = 0;
        foreach ($this->points as $point) {
            foreach ($point->getAdjacentNeighboursWithoutDiagonals() as $adjacentPoint) {
                if ($this->pointBelongsToDroplet($adjacentPoint) === false) {
                    $surface++;
                }
            }
        }

        return $surface;
    }

    public function calculateExteriorSurface(): int
    {
        $floodedPointsFromOutside = $this->getPointsFromOutside();

        $exteriorSurface = 0;
        foreach ($this->points as $point) {
            foreach ($point->getAdjacentNeighboursWithoutDiagonals() as $adjacentPoint) {
                if (in_array((string) $adjacentPoint, $floodedPointsFromOutside, true)) {
                    $exteriorSurface++;
                }
            }
        }

        return $exteriorSurface;
    }

    private function buildGraph(Space3DBoundary $spaceBoundary): array
    {
        $graph = [];

        $spaceBoundary->forEachPointInBoundary(function (Point3D $point, Space3DBoundary $spaceBoundary) use (&$graph) {
            $graph[(string) $point] = $this->getAvailableAdjacentNeighbours($point, $spaceBoundary);
        });

        return $graph;
    }

    private function getAvailableAdjacentNeighbours(Point3D $point, Space3DBoundary $space3DBoundary): array
    {
        $availableAdjacentNeighbours = [];

        foreach ($point->getAdjacentNeighboursWithoutDiagonals() as $adjacentPoint) {
            if ($space3DBoundary->isPointInside($adjacentPoint) === false) {
                continue;
            }

            if ($this->pointBelongsToDroplet($adjacentPoint) === false) {
                $availableAdjacentNeighbours[] = (string) $adjacentPoint;
            }
        }

        return $availableAdjacentNeighbours;
    }

    private function pointBelongsToDroplet(Point3D $point): bool
    {
        return isset($this->points[(string) $point]);
    }

    private function getPointsFromOutside(): array
    {
        $boundarySpaceExpandedByOne = Space3DBoundary::createForPoints(...$this->points)->expandBy(1);
        $lowestPossiblePoint = $boundarySpaceExpandedByOne->getLowestPossiblePoint();

        $graph = $this->buildGraph($boundarySpaceExpandedByOne);

        $floodFill = new FloodFill();

        return $floodFill->fill($graph, (string) $lowestPossiblePoint);
    }
}
