<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day20;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Grove Positioning System',
)]
final class Solution implements Solver
{
    private const ENCRYPTION_KEY = 811589153;

    public function solve(Input $input): Result
    {
        $listFirstPart = new MixingList($input->asArray());
        $listFirstPart->mix();

        $listSecondPart = new MixingList($input->asArray(), self::ENCRYPTION_KEY);
        $listSecondPart->mixTenTimes();

        return new Result(
            $listFirstPart->getGrooveCoordinatesValuesSum(),
            $listSecondPart->getGrooveCoordinatesValuesSum()
        );
    }
}
