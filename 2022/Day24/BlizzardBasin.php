<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day24;

use App\Utils\Collection;
use App\Utils\DirectionalLocation;
use App\Utils\Location;
use App\Utils\Range;
use App\Utils\Space2DBoundary;

class BlizzardBasin
{
    private array $blizzards;
    private Space2DBoundary $space2DBoundary;

    /**
     * @param DirectionalLocation[] $blizzards
     */
    public function __construct(
        array $blizzards,
        int $maxY,
        int $maxX,
    ) {
        $this->blizzards = $blizzards;
        $this->space2DBoundary = Space2DBoundary::fromPrimitives(1, 1, $maxX - 1, $maxY - 1);
    }

    public function getMinutesToReach(Location $start, Location $end): int
    {
        $minute = 0;

        $schroedingerElves = [$start];

        do {
            $this->moveBlizzards();
            $schroedingerElves = $this->generateNewElves($schroedingerElves);

            $minute++;

            $found = $this->doesAnyElvesReachEnd($schroedingerElves, $end);
            $schroedingerElves = $this->killElves($schroedingerElves, $start);
        } while ($found === false);

        return $minute;
    }

    private function moveBlizzards(): void
    {
        $newBlizzards = [];

        foreach ($this->blizzards as $blizzard) {
            $new = $blizzard->moveInDirection();
            $newBlizzards[] = $this->space2DBoundary->wrapAroundBoundariesIfRequired($new);
        }

        $this->blizzards = $newBlizzards;
    }

    /**
     * @param Location[] $positions
     * @return Location[]
     */
    private function generateNewElves(array $positions): array
    {
        $newElves = [];

        foreach ($positions as $position) {
            $newElves[] = $position;

            foreach ($position->getStraightAdjacent() as $loc) {
                $newElves[] = $loc;
            }
        }

        return array_unique($newElves);
    }

    /**
     * @param Location[] $elvesPositions
     * @return Location[]
     */
    private function killElves(array $elvesPositions, Location $start): array
    {
        $blizzardsLocations = Collection::create($this->blizzards)
            ->forEach(static fn (DirectionalLocation $location): string => (string) $location->location);

        $notKilledElves = [];

        foreach ($elvesPositions as $elf) {
            $notCollideWithBlizzard = $blizzardsLocations->contains((string) $elf) === false;

            if ($notCollideWithBlizzard) {
                $isInMapBoundary = $this->space2DBoundary->isInBoundary($elf);
                $isStartPosition = $elf->equals($start);

                if ($isInMapBoundary || $isStartPosition) {
                    $notKilledElves[] = $elf;
                }
            }
        }

        return array_unique($notKilledElves);
    }

    private function doesAnyElvesReachEnd(array $schroedingerElves, Location $end): bool
    {
        foreach ($schroedingerElves as $elf) {
            if ($elf->equals($end)) {
                return true;
            }
        }

        return false;
    }
}
