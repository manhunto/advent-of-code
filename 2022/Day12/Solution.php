<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day12;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\PathFinding\Dijkstra;

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

        $dijkstra = new Dijkstra();

        // Part 1
        $firstPathCosts = $dijkstra->calculateDistance($graph, (string) $end, [(string) $start]);
        $stepsToLocationWithBestSignal = $firstPathCosts[(string)$start];

        // Part 2
        $secondPartCosts = $dijkstra->calculateDistance($graph, (string) $end, $lowestPoints);

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
}
