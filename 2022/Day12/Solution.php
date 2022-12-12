<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day12;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use Taniko\Dijkstra\Graph;

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

        foreach ($grid as $y => $rows) {
            foreach ($rows as $x => $value) {
                $point = new Point($value, $y, $x);
                $grid[$y][$x] = $point;

                if ($point->isStart()) {
                    $start = $point;
                } elseif ($point->isEnd()) {
                    $end = $point;
                }
            }
        }

        $graph = $this->buildGraph($grid);

        // todo: reverse, end -> start
        // todo: part 1, to s
        // todo: part 2, min -> fore a

        $cost = $this->dijkstra($graph, $start, $end);

        return new Result($cost[$end]);
    }

    private function buildGraph(array $grid): array
    {
        $graph = [];

        foreach ($grid as $rows) {
            /** @var Point $point */
            foreach ($rows as $point) {
                $neighboursToMove = $point->getNeighboursToMove($grid);
                if (empty($neighboursToMove)) {
                    $graph[(string)$point] = [];
                } else {
                    foreach ($neighboursToMove as $neighbour) {
                        $graph[(string)$point][(string)$neighbour] = 1;
                    }
                }
            }
        }

        return $graph;
    }

    private function dijkstra(array $graph, Point $start): array
    {
        $unvisited = array_keys($graph);
        $distance = array_fill_keys($unvisited, PHP_INT_MAX);
        $distance[(string) $start] = 0;

        while (!empty($unvisited)) {
            var_dump(count($unvisited));
            $currentNode = $this->minDistanceNode($distance, $unvisited);
            unset($unvisited[array_search($currentNode, $unvisited, true)]);

            foreach ($graph[$currentNode] as $neighbour => $cost) {
                $distance[$neighbour] = $distance[$currentNode] + $cost;
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
