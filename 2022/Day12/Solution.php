<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day12;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\PathFinding\BreadthFirstSearch;

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

        $bfs = new BreadthFirstSearch();

        // Part 1
        $firstPathCosts = $bfs->getPath($graph, (string) $end, [(string) $start]);
        $stepsToLocationWithBestSignal = count($firstPathCosts) - 1;

        // Part 2
        $secondPartCosts = $bfs->getPath($graph, (string) $end, $lowestPoints);
        $stepsToTheFirstLowestLocation = count($secondPartCosts) - 1;

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
                foreach ($point->getNeighboursToMove($grid) as $neighbour) {
                    $graph[(string)$point][] = (string)$neighbour;
                }
            }
        }

        return $graph;
    }
}
