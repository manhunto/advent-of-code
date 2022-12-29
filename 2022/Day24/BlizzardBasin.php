<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day24;

use App\Utils\Collection;
use App\Utils\DirectionalLocation;
use App\Utils\Location;

class BlizzardBasin
{
    /**
     * @param DirectionalLocation[] $blizzards
     */
    public function __construct(
        private array $blizzards,
        private readonly int $maxY,
        private readonly int $maxX,
    ) {
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

            $newLocation = $new->location;

            if ($newLocation->x >= $this->maxX) {
                $new = $new->changeX(1);
            } elseif ($newLocation->x <= 0) {
                $new = $new->changeX($this->maxX - 1);
            } elseif ($newLocation->y <= 0) {
                $new = $new->changeY($this->maxY - 1);
            } elseif ($newLocation->y >= $this->maxY) {
                $new = $new->changeY(1);
            }

            $newBlizzards[] = $new;
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
     * @param Location[] $positions
     * @return Location[]
     */
    private function killElves(array $positions, Location $start): array
    {
        $blizzardsLocations = Collection::create($this->blizzards)
            ->forEach(static fn (DirectionalLocation $location): string => (string) $location->location);

        $possibleMoves = [];

        foreach ($positions as $position) {
            $notCollideWithBlizzard = $blizzardsLocations->contains((string) $position) === false;
            $isInMapBoundary = $position->x > 0 && $position->y > 0 && $position->x < $this->maxX && $position->y < $this->maxY;
            $isStartPosition = $position->equals($start);

            if ($notCollideWithBlizzard && ($isInMapBoundary || $isStartPosition)) {
                $possibleMoves[] = $position;
            }
        }

        return array_unique($possibleMoves);
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
