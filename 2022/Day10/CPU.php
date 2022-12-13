<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day10;

use App\Utils\Collection;

class CPU
{
    /**
     * @var int[]
     */
    private array $instructions = [];

    public function noop(): void
    {
        $this->instructions[] = 0;
    }

    public function addx(int $value): void
    {
        $this->instructions[] = 0;
        $this->instructions[] = $value;
    }

    public function getSignalStrengthAtCycles(int ...$cycleNumbers): array
    {
        $x = 1;
        $signalStrength = [];

        foreach ($this->getInstructionsWithCycleAsIndex() as $cycle => $instruction) {
            if (in_array($cycle, $cycleNumbers, true)) {
                $signalStrength[$cycle] = $cycle * $x;
            }

            $x += $instruction;
        }

        return $signalStrength;
    }

    public function getPixelsInRowsOnCRT(int $screenLength): array
    {
        $x = 1;
        $pixels = [];

        foreach ($this->getInstructionsWithCycleAsIndex() as $cycle => $instruction) {
            $spritePosition = [$x, $x + 1, $x + 2];

            if (in_array($cycle % $screenLength, $spritePosition, true)) {
                $pixels[] = '#';
            } else {
                $pixels[] = '.';
            }

            $x += $instruction;
        }

        return array_chunk($pixels, $screenLength);
    }

    private function getInstructionsWithCycleAsIndex(): array
    {
        return Collection::withOneAsFirstIndex($this->instructions);
    }
}
