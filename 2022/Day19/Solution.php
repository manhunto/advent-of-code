<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day19;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Output\Console as C;

#[SolutionAttribute(
    name: 'Not Enough Minerals',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $maxMinutes = 24;
        $bluePrints = $this->getBluePrints($input);
        $bluePrint = $bluePrints[1];
        $costs = [
            'ore' => ['ore' => $bluePrint[0]],
            'clay' => ['ore' => $bluePrint[1]],
            'obsidian' => ['ore' => $bluePrint[2], 'clay' => $bluePrint[3]],
            'geode' => ['ore' => $bluePrint[4], 'obsidian' => $bluePrint[5]]
        ];
        $factory = new Factory($costs);

        $checker = new FactoryChecker();
        $result = $checker->howMuchGeocodeCanProduce($factory, $maxMinutes);

        return new Result($result);
    }

    /**
     * @param Input $input
     * @return array
     */
    private function getBluePrints(Input $input): array
    {
        preg_match_all('/Each ore robot costs (\d+) ore./', $input->asString(), $oreRobotCosts);
        preg_match_all('/Each clay robot costs (\d+) ore./', $input->asString(), $clayRobotCosts);
        preg_match_all('/Each obsidian robot costs (\d+) ore and (\d+) clay/', $input->asString(), $obsidianRobotCosts);
        preg_match_all('/Each geode robot costs (\d+) ore and (\d+) obsidian./', $input->asString(), $geodeRobotCosts);

        $blueprintAsArrays = Collection::create([
            $oreRobotCosts[1],
            $clayRobotCosts[1],
            $obsidianRobotCosts[1],
            $obsidianRobotCosts[2],
            $geodeRobotCosts[1],
            $geodeRobotCosts[2],
        ]);

        $c = $blueprintAsArrays
            ->forEach(static fn(array $row): int => count($row))
            ->unique();

        if ($c->count() !== 1) {
            throw new \LogicException('Invalid blueprint input');
        }

        $bluePrints = [];

        for ($i = 1; $i <= $c->get(0); $i++) {
            $costs = $blueprintAsArrays
                ->forEach(static fn(array $row) => (int)$row[$i - 1])
                ->toArray();

            $bluePrints[] = $costs;
        }
        return $bluePrints;
    }
}
