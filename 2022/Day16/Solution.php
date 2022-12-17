<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\PathFinding\EveryPossiblePathGenerator;
use App\Utils\PathFinding\Node;

#[SolutionAttribute(
    name: 'Proboscidea Volcanium',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $valves = $this->parseValves($input);

        $nodes = [];

        foreach ($valves as $valve) {
            $nodes[] = new Node($valve->name, $valve->neighbourValves);

            if ($valve->canValveBeOpen()) {
                $nodes[] = new Node(
                    $valve->name . '-open-valve',
                    [...$valve->neighbourValves, $valve->name],
                    visitableOnlyOnce: true
                );
            }
        }

        $generator = new EveryPossiblePathGenerator($nodes, 'AA', 30);

        /** @var array $path */
        $paths = $generator->generate();

        return new Result(123);
    }

    /**
     * @return Valve[]
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
