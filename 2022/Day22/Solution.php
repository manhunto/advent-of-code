<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day22;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Map;
use App\Utils\Location;
use App\Utils\Rotation;

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

        /** @var Location $myPosition */
        $myPosition = $map->findFirst('.');
        $finalPasswordFirstPart = $this->solveFirstPart($myPosition, $instructions, $map);
        $finalPasswordSecondPart = $this->solveSecondPart($myPosition, $instructions, $map);

        return new Result($finalPasswordFirstPart, $finalPasswordSecondPart);
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

    private function getRotation(string $instruction): Rotation
    {
        return match ($instruction) {
            'R' => Rotation::CLOCKWISE,
            'L' => Rotation::ANTICLOCKWISE,
            default => throw new \LogicException('Unexpected instruction for rotation')
        };
    }

    private function solveFirstPart(Location $myPosition, array $instructions, Map $map): int
    {
        $player = new Player($myPosition);

        foreach ($instructions as $instruction) {
            if (is_numeric($instruction)) {
                $player->moveForward((int)$instruction, $map);
            } else {
                $rotation = $this->getRotation($instruction);

                $player->rotate($rotation);
            }
        }

        return $player->getFinalPassword();
    }

    private function solveSecondPart(Location $myPosition, array $instructions, Map $map): int
    {
        $mapElements = ['.', '#'];
        $walkableElements = ['.'];

        $mapper = new CubeEdgeMapper($map, $mapElements);
        $walkableCube = new WalkableCube($walkableElements, $map, $mapElements, $mapper->getEdgeMap());
        $player = new Player($myPosition);

        foreach ($instructions as $instruction) {
            if (is_numeric($instruction)) {
                $player->moveForwardOnCube((int)$instruction, $walkableCube);
            } else {
                $rotation = $this->getRotation($instruction);

                $player->rotate($rotation);
            }
        }

        return $player->getFinalPassword();
    }

}
