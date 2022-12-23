<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Map;
use App\Utils\Output\Console as C;

#[SolutionAttribute(
    name: 'Monkey Map',
)]
final class Solution implements Solver
{
    private const ABYSS = ' ';

    public function solve(Input $input): Result
    {
        /** @var Map $map */
        [$map, $instructions] = $this->parseInput($input);

        $myPosition = $map->findFirst('.');

        $player = new Player($myPosition);

        foreach ($instructions as $instruction) {
            if ($instruction === 'R') {
                $player->turnClockwise();
            } elseif ($instruction === 'L') {
                $player->turnAntiClockwise();
            } else {
                $player->moveForward((int) $instruction, $map);
            }
        }

        $printer = $map->printer()
            ->naturalHorizontally();

        foreach ($player->history as $history) {
            $printer->drawTemporaryPoint($history[0], $history[1]);
        }

        $printer->print();

        return new Result($player->getFinalPassword());
    }

    private function parseInput(Input $input): array
    {
        $instructions = null;
        $mapRow = [];

        foreach ($input->asArrayWithoutEmptyLines() as $row) {
            if (preg_match('/[0-9RL]+/', $row)) {
                $instructions = $row;
            } else {
                $mapRow[] = str_split($row);
            }
        }

        $map = Map::generateForRowsAndFilledNonExisting($mapRow, self::ABYSS);

        preg_match_all('/\d+|R|L/', $instructions, $matches);

        return [$map, $matches[0]];
    }
}
