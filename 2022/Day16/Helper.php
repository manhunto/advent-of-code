<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

class Helper
{
    private const OPEN_INSTRUCTION_SUFFIX = '-open';

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
                $instruction[] = $nextValve->name . self::OPEN_INSTRUCTION_SUFFIX;
            }
            $prevValveName = $nextValveName;
        }

        return $instruction;
    }

    public function calculatePressureReleased(array $path, array $valves, array $pathFromValveToValve, int $minutes): int
    {
        $instructions = $this->convertPathBetweenOpenableValvesToFullPath(
            $path,
            $valves,
            $pathFromValveToValve,
        );

        $releasedPressure = 0;
        $minutesLeft = $minutes;
        foreach ($instructions as $move) {
            if (str_ends_with($move, self::OPEN_INSTRUCTION_SUFFIX)) {
                $valveName = str_replace(self::OPEN_INSTRUCTION_SUFFIX, '', $move);
                /** @var Valve $valve */
                $valve = $valves[$valveName];
                $releasedPressure += $valve->calculateReleasedPressure($minutesLeft);
            }

            $minutesLeft--;
        }

        return $releasedPressure;
    }
}
