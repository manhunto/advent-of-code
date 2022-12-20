<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day18;

use App\Utils\PathFinding\BreadthFirstSearch;
use App\Utils\Point3D;
use App\Utils\Range;

class Droplet
{
    private const ADJACENT_GRID = [
        [1, 0, 0],
        [0, 1, 0],
        [0, 0, 1],
        [-1, 0, 0],
        [0, -1, 0],
        [0, 0, -1],
    ];

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
            foreach (self::ADJACENT_GRID as $adjacentPosition) {
                $adjacentPoint = $point->move($adjacentPosition[0], $adjacentPosition[1], $adjacentPosition[2]);

                if (isset($this->points[(string) $adjacentPoint]) === false) {
                    $surface++;
                }
            }
        }

        return $surface;
    }

    public function calculateExteriorSurface(): int
    {
        $surface = 0;

        $boundaryPoints = $this->getBoundaryCubePoints();
        $graph = $this->buildGraph();

        $bfs = new BreadthFirstSearch(); // todo use flood fill alogrithm

        $count = count($this->points);

        $i = 0;
        foreach ($this->points as $point) {
            echo number_format(++$i / $count * 100, 2). PHP_EOL;

            $closestBoundaryPoint = $this->getClosesBoundaryPoint($point, $boundaryPoints);

            foreach (self::ADJACENT_GRID as $adjacentPosition) {
                $adjacentPoint = $point->move($adjacentPosition[0], $adjacentPosition[1], $adjacentPosition[2]);

                if (isset($this->points[(string) $adjacentPoint]) === false) {
                    try {
                        $path = $bfs->getPath($graph, (string) $adjacentPoint, [(string) $closestBoundaryPoint]);

                        if (!empty($path)) {
                            $surface++;
                        }
                    } catch (\LogicException $e) {
                    }

                }
            }
        }

        return $surface;
    }

    private function getBoundaryCubePoints(): array
    {
        [$x, $y, $z] = $this->getBoundaryRanges();

        return [
            new Point3D($x->from, $y->from, $z->from),
            new Point3D($x->from, $y->to, $z->from),
            new Point3D($x->from, $y->to, $z->to),
            new Point3D($x->from, $y->from, $z->to),
            new Point3D($x->to, $y->to, $z->to),
            new Point3D($x->to, $y->from, $z->from),
            new Point3D($x->to, $y->to, $z->from),
            new Point3D($x->to, $y->from, $z->to),
        ];
    }

    /**
     * @param Point3D[] $boundaryPoints
     */
    private function getClosesBoundaryPoint(Point3D $point, array $boundaryPoints): Point3D
    {
        $dist = PHP_INT_MAX;
        $closest = null;

        foreach ($boundaryPoints as $boundaryPoint) {
            $distanceToBoundary = $point->manhattanDistance($boundaryPoint);
            if ($distanceToBoundary < $dist) {
                $dist = $distanceToBoundary;
                $closest = $boundaryPoint;
            }
        }

        return $closest;
    }

    private function buildGraph(): array
    {

        $boundaries = $this->getBoundaryRanges();
        [$rangeX, $rangeY, $rangeZ] = $boundaries;

        $graph = [];
        foreach ($rangeX->getItems() as $x) {
            foreach ($rangeY->getItems() as $y) {
                foreach ($rangeZ->getItems() as $z) {
                    $point = new Point3D($x, $y, $z);

                    $availableMoves = [];
                    foreach (self::ADJACENT_GRID as $adjacentPosition) {
                        $adjacentPoint = $point->move($adjacentPosition[0], $adjacentPosition[1], $adjacentPosition[2]);

                        if ($this->isOutsideBoundaries($adjacentPoint, $boundaries)) {
                            continue;
                        }

                        if (isset($this->points[(string) $adjacentPoint]) === false) {
                            $availableMoves[] = (string) $adjacentPoint;
                        }
                    }

                    $graph[(string) $point] = $availableMoves;
                }
            }
        }

        return $graph;
    }

    /**
     * @return Range[]
     */
    private function getBoundaryRanges(): array
    {
        $points = $this->points;

        $firstPoint = array_shift($points);

        $rangeX = Range::createForPoint($firstPoint->x);
        $rangeY = Range::createForPoint($firstPoint->y);
        $rangeZ = Range::createForPoint($firstPoint->z);


        foreach ($points as $point) {
            $rangeX = $rangeX->expandTo($point->x - 1)->expandTo($point->x + 1);
            $rangeY = $rangeY->expandTo($point->y - 1)->expandTo($point->y + 1);
            $rangeZ = $rangeZ->expandTo($point->z - 1)->expandTo($point->z + 1);
        }

        return [$rangeX, $rangeY, $rangeZ];
    }

    /**
     * @param Range[] $boundaries
     */
    private function isOutsideBoundaries(Point3D $point, array $boundaries): bool
    {
        [$x, $y, $z] = $boundaries;

        return $x->isNumberInRange($point->x) === false
            || $y->isNumberInRange($point->y) === false
            || $z->isNumberInRange($point->z) === false;
    }
}
