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
        $map = $this->generateMap();

        $proposedMoves = [];

        $anyElfMoves = false;

        foreach ($this->elvesLocations as $elfLocation) {
            if ($this->hasAdjacentElf($elfLocation, $map)) {
                $proposedMoves[] = [$elfLocation, $elfLocation];

                continue;
            }

            $newLocation = $this->moveElfToNewLocation($elfLocation, $map);
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

    private function hasAdjacentElf(Location $loc, Map $map): bool
    {
        foreach ($loc->getAllAdjacentLocations() as $adjacent) {
            if ($map->hasElementOnPoint($adjacent, self::ELF_POSITION)) {
                return false;
            }
        }

        return true;
    }

    private function moveElfToNewLocation(Location $loc, Map $map): ?Location
    {
        foreach ($this->directions as $direction) {
            if ($this->hasAnyElfInDirection($loc, $direction, $map) === false) {
                return $loc->moveInDirection($direction);
            }
        }

        return null;
    }

    private function hasAnyElfInDirection(Location $loc, Direction $direction, Map $map): bool
    {
        foreach ($loc->getAllAdjacentLocationsInDirection($direction) as $adjacent) {
            if ($map->hasElementOnPoint($adjacent, self::ELF_POSITION)) {
                return true;
            }
        }

        return false;
    }

    private function moveOnlyPossible(array $newLocations): array
    {
        $newElvesLocations = [];

        foreach ($newLocations as $index => $newLocation) {
            /**
             * @var Location $old
             * @var Location $new
             */
            [$old, $new] = $newLocation;

            if ($new === null) {
                $newElvesLocations[] = $old;
            } else {
                $newLocationsWithoutCurrent = Collection::create($newLocations)
                    ->unsetKeys([$index])
                    ->forEach(static fn (array $row): Location => $row[1]);

                if ($newLocationsWithoutCurrent->searchKey(static fn (Location $search): bool => $search->equals($new))) {
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

            Console::writeln($roundNumber);
        }

        return $roundNumber;
    }
}
