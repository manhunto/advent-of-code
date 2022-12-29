<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day24;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Direction;
use App\Utils\DirectionalLocation;
use App\Utils\Location;
use App\Utils\Map;
use App\Utils\Output\Console as C;

#[SolutionAttribute(
    name: 'Blizzard Basin',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $inputAsArray = $input->asArray();
        $rows = count($inputAsArray) - 1;
        $columns = strlen($inputAsArray[0]) - 1;

        $map = Map::generateFilled($rows, $columns, '.');

        $blizzards = [];

        foreach ($inputAsArray as $y => $row) {
            foreach (str_split($row) as $x => $item) {
                if ($item === '#') {
                    $map->draw($y, $x, '#');
                } elseif ($item !== '.') {
                    $blizzards[] = DirectionalLocation::fromPrimitiveLocation($x, $y, Direction::tryFromString($item));
                }
            }
        }

        $myPosition = $map->findFirst('.');
        $positions = [$myPosition];
        $end = $map->findLast('.');

        $minute = 0;

//        $this->print($map, $positions, $blizzards);

//        C::wait();


        do {
            $positions = $this->generateNewPositions($positions);
            $blizzards = $this->moveBlizzards($blizzards, $rows, $columns);
            $blizzardsLocations = Collection::create($blizzards)
                ->forEach(static fn (DirectionalLocation $location): string => (string) $location->location);

            $found = false;

            $minute++;

            foreach ($positions as $newPosition) {
                if ($newPosition->equals($end)) {
                    $found = true;
                    break;
                }
            }

            $positions = $this->killPositions($positions, $blizzardsLocations, $rows, $columns);

            C::writeln($minute);
//            $this->print($map, $positions, $blizzards);
//
//            C::wait();

        } while ($found === false);


        return new Result($minute);
    }

    /**
     * @param DirectionalLocation[] $blizzards
     * @return DirectionalLocation[]
     */
    private function moveBlizzards(array $blizzards, int $rows, int $columns): array
    {
        $newLocations = [];

        foreach ($blizzards as $blizzard) {
            $new = $blizzard->moveInDirection();

            $newLocation = $new->location;

            if ($newLocation->x >= $columns) {
                $new = $new->changeX(1);
            } elseif ($newLocation->x <= 0) {
                $new = $new->changeX($columns - 1);
            } elseif ($newLocation->y <= 0) {
                $new = $new->changeY($rows - 1);
            } elseif ($newLocation->y >= $rows) {
                $new = $new->changeY(1);
            }

            $newLocations[] = $new;
        }

        return $newLocations;
    }

    /**
     * @param Location[] $positions
     * @return Location[]
     */
    private function generateNewPositions(array $positions): array
    {
        $newPositions = [];

        foreach ($positions as $position) {
            $newPositions[] = $position;

            foreach ($position->getStraightAdjacent() as $loc) {
                $newPositions[] = $loc;
            }
        }

        return array_unique($newPositions);
    }

    /**
     * @param Location[] $positions
     * @return Location[]
     */
    private function killPositions(array $positions, Collection $blizzardsLocations, int $rows, int $columns): array
    {
        $possibleMoves = [];

        foreach ($positions as $position) {
            if ($blizzardsLocations->contains((string) $position) === false) {
                if ($position->x > 0 && $position->y > 0 && $position->x < $columns && $position->y < $rows) {
                    $possibleMoves[] = $position;
                } elseif ($position->x === 1 && $position->y === 0) { // hack to start position
                    $possibleMoves[] = $position;
                }
            }
        }

        return array_unique($possibleMoves);
    }

    private function print(Map $map, array $positions, array $blizzards): void
    {
        $printer = $map->printer();

        foreach ($positions as $position) {
            $printer->drawTemporaryPoint($position, 'E');
        }

        foreach ($blizzards as $blizzard) {
            $printer->drawTemporaryPointWithCounter($blizzard->location, $blizzard->direction->asString(), '.');
        }

        $printer->naturalHorizontally()
            ->withoutRowNumbers()
            ->print();
    }
}
