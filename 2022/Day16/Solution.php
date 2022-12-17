<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
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

    public function solve(Input $input): Result
    {
        $valves = $this->parseValves($input);

        $graph = [];
        foreach ($valves as $valve) {
            $graph[$valve->name] = $valve->neighbourValves;
        }

        $valvesToOpen = Collection::create($valves)
            ->filter(static fn (Valve $valve) => $valve->canValveBeOpen())
            ->add($valves[self::START_NODE])
            ->forEach(static fn (Valve $valve) => $valve->name)
            ->values()
            ->toArray();

        $pathFromValveToValve = [];
        $bfs = new BreadthFirstSearch();
        foreach ($valvesToOpen as $fromValve) {
            foreach ($valvesToOpen as $toValve) {
                if ($fromValve !== $toValve) {
                    $path = $bfs->getPath($graph, $fromValve, [$toValve]);

                    $collection = Collection::create($path)
                        ->removeAtBeginning(1)
                        ->toArray();

                    $pathFromValveToValve[$fromValve . '-' . $toValve] = $collection;
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

        $generator = new EveryPossiblePathGenerator($nodes, self::START_NODE);
        $paths = $generator->generate();

        var_dump(count($paths));

        $instructions = [];
        foreach ($paths as $path) {
            $instruction = [];
            $prevValveName = array_shift($path);
            $instruction[] = $prevValveName;

            foreach ($path as $nextValveName) {
                $nextValve = $valves[$nextValveName];
                $pathPrevNext = $pathFromValveToValve[$prevValveName . '-' . $nextValveName];
                $instruction = [...$instruction, ...$pathPrevNext];
                if ($nextValve->canValveBeOpen()) {
                    $instruction[] = $nextValve->name . '-open';
                }
                $prevValveName = $nextValveName;
            }

            $instructions[] = $instruction;
        }

        $maxReleasedPressure = 0;
        $bestPath = [];

        var_dump(count($instructions));

        foreach ($instructions as $instruction) {
            $releasedPressure = 0;
            $minutesLeft = 30;
            foreach ($instruction as $move) {
                if ($minutesLeft <= 0) { 
                    break;
                }

                if (str_ends_with($move, '-open')) {
                    $valveName = str_replace('-open', '', $move);
                    /** @var Valve $valve */
                    $valve = $valves[$valveName];
                    $releasedPressure += $valve->calculateReleasedPressure($minutesLeft);
                }
                $minutesLeft--;
            }

            if ($maxReleasedPressure < $releasedPressure) {
                $maxReleasedPressure = $releasedPressure;
                $bestPath = $instruction;
            }
        }

        var_dump($maxReleasedPressure);
        print_r($bestPath);


        return new Result($maxReleasedPressure);
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

                $valves[$name] = new Valve($name, (int) $flowRate, explode(', ', $neighbourValves));
            } else {
                throw new \LogicException('Invalid regex. Cannot parse valve');
            }
        }
        return $valves;
    }
}
