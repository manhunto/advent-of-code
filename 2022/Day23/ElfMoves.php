<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day23;

use App\Input;
use App\Utils\Collection;
use App\Utils\Direction;
use App\Utils\Location;
use App\Utils\Map;
use App\Utils\Output\Console;

class ElfMoves
{
    private const ELF_POSITION = '#';
    private const GROUND_POSITION = '.';

    /** @var Direction[] */
    private array $directions;

    /**
     * @param Location[] $elvesLocations
     */
    public function __construct(
        private array $elvesLocations
    ) {
        $this->directions = [
            Direction::NORTH,
            Direction::SOUTH,
            Direction::WEST,
            Direction::EAST,
        ];
    }

    public static function fromInput(Input $input): self
    {
        $locations = [];

        foreach ($input->asArray() as $y => $row) {
            foreach (str_split($row) as $x => $item) {
                if ($item === self::ELF_POSITION) {
                    $locations[] = new Location($x, $y);
                }
            }
        }

        return new self($locations);
    }

    public function move(): bool
    {
        $proposedMoves = [];

        $anyElfMoves = false;

        $elvesAsString = Collection::create($this->elvesLocations)
            ->eachToString();

        foreach ($this->elvesLocations as $index => $elfLocation) {
            $otherElves = $elvesAsString
                ->unsetKeys([$index]);

            if ($this->hasAdjacentElf($elfLocation, $otherElves)) {
                $proposedMoves[] = [$elfLocation, $elfLocation];

                continue;
            }

            $newLocation = $this->moveElfToNewLocation($elfLocation, $otherElves);
            if ($newLocation === null ){
                $proposedMoves[] = [$elfLocation, $elfLocation];
            } else {
                $proposedMoves[] = [$elfLocation, $newLocation];
            }

            $anyElfMoves = true;
        }

        $this->elvesLocations = $this->moveOnlyPossible($proposedMoves);
        $this->directions = Collection::create($this->directions)
            ->moveFirstToEnd()
            ->toArray()
        ;

        return $anyElfMoves;
    }

    public function generateMap(): Map
    {
        return Map::generateForLocations($this->elvesLocations, self::ELF_POSITION, self::GROUND_POSITION);
    }

    private function hasAdjacentElf(Location $loc, Collection $otherElves): bool
    {
        foreach ($loc->getAllAdjacentLocations() as $adjacent) {
            if ($otherElves->contains((string) $adjacent)) {
                return false;
            }
        }

        return true;
    }

    private function moveElfToNewLocation(Location $loc, Collection $otherElves): ?Location
    {
        foreach ($this->directions as $direction) {
            if ($this->hasAnyElfInDirection($loc, $direction, $otherElves) === false) {
                return $loc->moveInDirection($direction);
            }
        }

        return null;
    }

    private function hasAnyElfInDirection(Location $loc, Direction $direction, Collection $otherElves): bool
    {
        foreach ($loc->getAllAdjacentLocationsInDirection($direction) as $adjacent) {
            if ($otherElves->contains((string) $adjacent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, array{0: Location, 1: Location}> $newLocations
     * @return Location[]
     */
    private function moveOnlyPossible(array $newLocations): array
    {
        $newElvesLocations = [];

        $newLocationsAsStrings = Collection::create($newLocations)
            ->forEach(static fn (array $row): string => (string) $row[1]);

        foreach ($newLocations as $index => $newLocation) {
            [$old, $new] = $newLocation;

            if ($new === null) {
                $newElvesLocations[] = $old;
            } else {
                $newLocationsWithoutCurrent = $newLocationsAsStrings
                    ->unsetKeys([$index]);

                if ($newLocationsWithoutCurrent->contains((string) $new)) {
                    $newElvesLocations[] = $old;
                } else {
                    $newElvesLocations[] = $new;
                }
            }
        }

        return $newElvesLocations;
    }

    public function moveNTimes(int $maxMoves): void
    {
        $move = 0;

        do {
            $this->move();

            $move++;
        } while ($move < $maxMoves);
    }

    public function countEmptyGround(): int
    {
        $map = $this->generateMap();

        return $map->calculateAreaForElements([self::GROUND_POSITION]);
    }

    public function getNumberOfRoundWhereNoElfMoves(): int
    {
        $roundNumber = 1;

        while ($this->move()) {
            $roundNumber++;
        }

        return $roundNumber;
    }
}
