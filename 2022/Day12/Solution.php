<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day12;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Hill Climbing Algorithm',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $grid = $input->asGrid();
        /** @var Point|null $start */
        $start = null;
        /** @var Point|null $end */
        $end = null;

        $lowestPoints = [];

        foreach ($grid as $y => $rows) {
            foreach ($rows as $x => $value) {
                $point = new Point($value, $y, $x);
                $grid[$y][$x] = $point;

                if ($point->isStart()) {
                    $start = $point;
                } elseif ($point->isEnd()) {
                    $end = $point;
                } elseif ($point->isTheLowestElevation()) {
                    $lowestPoints[] = (string)$point;
                }
            }
        }

        $graph = $this->buildGraph($grid);

        // Part 1
        $firstPathCosts = $this->dijkstra($graph, $end, [(string) $start]);
        $stepsToLocationWithBestSignal = $firstPathCosts[(string)$start];

        // Part 2
        $secondPartCosts = $this->dijkstra($graph, $end, $lowestPoints);

        $aCost = array_filter(
            $secondPartCosts,
            static fn (string $key) => in_array($key, $lowestPoints, true),
            ARRAY_FILTER_USE_KEY
        );
        $stepsToTheFirstLowestLocation = min($aCost);

        return new Result(
            $stepsToLocationWithBestSignal,
            $stepsToTheFirstLowestLocation
        );
    }

    private function buildGraph(array $grid): array
    {
        $graph = [];

        foreach ($grid as $rows) {
            /** @var Point $point */
            foreach ($rows as $point) {
                $neighboursToMove = $point->getNeighboursToMove($grid);

                foreach ($neighboursToMove as $neighbour) {
                    $graph[(string)$point][(string)$neighbour] = 1;
                }
            }
        }

        return $graph;
    }

    private function dijkstra(array $graph, Point $start, array $stopAt = []): array
    {
        $unvisited = array_keys($graph);
        $distance = array_fill_keys($unvisited, PHP_INT_MAX);
        $distance[(string) $start] = 0;

        while (!empty($unvisited)) {
            $currentNode = $this->minDistanceNode($distance, $unvisited);
            unset($unvisited[array_search($currentNode, $unvisited, true)]);

            foreach ($graph[$currentNode] as $neighbour => $cost) {
                $distance[$neighbour] = $distance[$currentNode] + $cost;
            }

            if (!empty($stopAt) && in_array($currentNode, $stopAt, true)) {
                break;
            }
        }

        return $distance;
    }

    private function minDistanceNode(array $distance, array $unvisited): string
    {
        $min = PHP_INT_MAX;
        $next = null;

        foreach ($distance as $nodeName => $currentDistance) {
            if ($currentDistance <= $min && in_array($nodeName, $unvisited, true)) {
                $min = $currentDistance;
                $next = $nodeName;
            }
        }

        return $next;
    }
}
