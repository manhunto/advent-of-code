<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

class ValveHelpers
{
    /**
     * @param string[] $path
     * @param Valve[] $valves
     * @param array<string, string[]> $pathFromValveToValve
     * @return string[]
     */
    public function convertPathBetweenOpenableValvesToFullPath(array $path, array $valves, array $pathFromValveToValve): array
    {
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

        return $instruction;
    }
}
