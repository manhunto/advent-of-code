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
    private const SECOND_PART_MINUTES = 32;
    private const FIRST_PART_MINUTES = 24;

    public function solve(Input $input): Result
    {
        $bluePrints = $this->getBluePrints($input);

        $firstPart = $this->solveFirstPart($bluePrints);
        $secondPart = $this->solveSecondPart($bluePrints);

        return new Result($firstPart, $secondPart);
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
            $bluePrint = $blueprintAsArrays
                ->forEach(static fn(array $row) => (int) $row[$i - 1])
                ->toArray();

            $bluePrints[$i] = [
                'ore' => ['ore' => $bluePrint[0]],
                'clay' => ['ore' => $bluePrint[1]],
                'obsidian' => ['ore' => $bluePrint[2], 'clay' => $bluePrint[3]],
                'geode' => ['ore' => $bluePrint[4], 'obsidian' => $bluePrint[5]]
            ];
        }

        return $bluePrints;
    }

    private function solveFirstPart(array $bluePrints): int
    {
        $checker = new FactoryChecker();

        $result = 0;
        foreach ($bluePrints as $no => $bluePrint) {
            $geocodes = $checker->howMuchGeocodeCanProduce($bluePrint, self::FIRST_PART_MINUTES);

            $result += $no * $geocodes;
        }

        return $result;
    }

    private function solveSecondPart(array $blueprints): int
    {
        $checker = new FactoryChecker();
        $firstThreeBlueprints = array_slice($blueprints, 0, 3);

        $geocodes = [];
        foreach ($firstThreeBlueprints as $bluePrint) {
            $geocodes[] = $checker->howMuchGeocodeCanProduce($bluePrint, self::SECOND_PART_MINUTES);
        }

        return Collection::create($geocodes)
            ->multiply();
    }
}
