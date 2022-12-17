<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Arrays;
use App\Utils\Collection;
use App\Utils\PathFinding\BreadthFirstSearch;
use App\Utils\PathFinding\EveryPossiblePathGenerator;
use App\Utils\PathFinding\Node;

#[SolutionAttribute(
    name: 'Proboscidea Volcanium',
)]
final class Solution implements Solver
{
    private const START_NODE = 'AA';

    private Helper $helper;

    public function __construct()
    {
        $this->helper = new Helper();
    }

    public function solve(Input $input): Result
    {
        $valves = $this->parseValves($input);

        $graph = [];
        foreach ($valves as $valve) {
            $graph[$valve->name] = $valve->neighbourValves;
        }

        $valvesToOpen = Collection::create($valves)
            ->filter(static fn(Valve $valve) => $valve->canValveBeOpen())
            ->add($valves[self::START_NODE])
            ->forEach(static fn(Valve $valve) => $valve->name)
            ->values()
            ->toArray();

        $pathFromValveToValve = [];
        $bfs = new BreadthFirstSearch();
        foreach ($valvesToOpen as $fromValve) {
            foreach ($valvesToOpen as $toValve) {
                if ($fromValve !== $toValve) {
                    $path = $bfs->getPath($graph, $fromValve, [$toValve]);

                    $pathWithoutStartNode = Collection::create($path)
                        ->removeAtBeginning(1)
                        ->toArray();

                    $pathFromValveToValve[$fromValve . '-' . $toValve] = $pathWithoutStartNode;
                }
            }
        }

        $nodes = [];
        $valvesToOpen[] = self::START_NODE;

        foreach ($valvesToOpen as $valveToOpen) {
            $allValvesWithoutCurrent = Collection::create($valvesToOpen)
                ->removeItem($valveToOpen)
                ->toArray();

            $nodes[] = new Node($valveToOpen, $allValvesWithoutCurrent);
        }

        $firstPart = $this->solveFirstPart($nodes, $valves, $pathFromValveToValve);
        $secondPart = $this->solveSecondPart($nodes, $valves, $pathFromValveToValve);

        return new Result($firstPart, $secondPart);
    }

    /**
     * @return array<string, Valve>
     */
    private function parseValves(Input $input): array
    {
        $valves = [];

        foreach ($input->asArray() as $row) {
            if (preg_match('/Valve ([A-Z]{2}) has flow rate=(\d+); tunnels? leads? to valves? ([A-Z\s,]+)/', $row, $matches)) {
                [, $name, $flowRate, $neighbourValves] = $matches;

                $valves[$name] = new Valve($name, (int)$flowRate, explode(', ', $neighbourValves));
            } else {
                throw new \LogicException('Invalid regex. Cannot parse valve');
            }
        }
        return $valves;
    }

    private function solveFirstPart(array $nodes, array $valves, array $pathFromValveToValve): int
    {
        $minutes = 30;
        $paths = $this->generateAllPossiblePathsBetweenValves($nodes, $valves, $pathFromValveToValve, $minutes);

        $maxReleasedPressure = 0;

        foreach ($paths as $path) {
            $releasedPressure = $this->helper->calculatePressureReleased($path, $valves, $pathFromValveToValve, $minutes);

            $maxReleasedPressure = max($maxReleasedPressure, $releasedPressure);
        }

        return $maxReleasedPressure;
    }

    private function solveSecondPart(array $nodes, array $valves, array $pathFromValveToValve): int
    {
        $minutes = 26;
        $myPaths = $this->generateAllPossiblePathsBetweenValves($nodes, $valves, $pathFromValveToValve, $minutes);

        $maxReleasedPressure = 0;

        $scores = [];
        $paths = [];

        foreach ($myPaths as $myPath) {
            $sorted = Arrays::sortedAsc($myPath);

            $key = md5(serialize($sorted));

            $score = $this->helper->calculatePressureReleased($myPath, $valves, $pathFromValveToValve, $minutes);
            $scores[$key] = max($scores[$key] ?? 0, $score);
            $paths[$key] = $sorted;
        }

        $key = 0;
        foreach ($paths as $myKey => $my) {
            $elephantPaths = Collection::create($paths)
                ->removeAtBeginning(++$key)
                ->toArray();

            foreach ($elephantPaths as $elKey => $El) {
                $tA = $my;
                $tB = $El;
                unset($tA[0], $tB[0]);

                if (empty(array_intersect($tA, $tB))) {
                    $releasedPressure = $scores[$myKey] + $scores[$elKey];

                    $maxReleasedPressure = max($maxReleasedPressure, $releasedPressure);
                }
            }
        }

        return $maxReleasedPressure;
    }

    private function generateAllPossiblePathsBetweenValves(array $nodes, array $valves, array $pathFromValveToValve, int $minutes): array
    {
        $generator = new EveryPossiblePathGenerator(
            $nodes,
            self::START_NODE,
            new CheckPathExceedNMinutes(
                $valves,
                $pathFromValveToValve,
                $minutes
            )
        );

        return $generator->generate();
    }
}
