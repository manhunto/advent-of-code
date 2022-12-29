<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day24;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Direction;
use App\Utils\DirectionalLocation;
use App\Utils\Location;
use App\Utils\Map;

#[SolutionAttribute(
    name: 'Blizzard Basin',
)]
final class Solution implements Solver
{
    private const WALL = '#';
    private const EMPTY_SPACE = '.';

    public function solve(Input $input): Result
    {
        $inputAsArray = $input->asArray();
        $rows = count($inputAsArray) - 1;
        $columns = strlen($inputAsArray[0]) - 1;

        $map = Map::generateFilled($rows, $columns, self::EMPTY_SPACE);

        $blizzards = [];

        foreach ($inputAsArray as $y => $row) {
            foreach (str_split($row) as $x => $item) {
                if ($item === self::WALL) {
                    $map->draw($y, $x, self::WALL);
                } elseif ($item !== self::EMPTY_SPACE) {
                    $blizzards[] = DirectionalLocation::fromPrimitiveLocation($x, $y, Direction::tryFromString($item));
                }
            }
        }

        $basin = new BlizzardBasin($blizzards, $rows, $columns);

        /** @var Location $start */
        $start = $map->findFirst(self::EMPTY_SPACE);
        /** @var Location $end */
        $end = $map->findLast(self::EMPTY_SPACE);

        $fromStartToEnd = $basin->getMinutesToReach($start, $end);
        $backForSnacks = $basin->getMinutesToReach($end, $start);
        $backToEnd = $basin->getMinutesToReach($start, $end);

        return new Result(
            $fromStartToEnd,
            $fromStartToEnd + $backForSnacks + $backToEnd
        );
    }
}
