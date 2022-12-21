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
    public function solve(Input $input): Result
    {
        $list = new MixingList($input->asArray());
        $list->mix();

        $sum = $list->getNumberNAfterZero(1000)
            + $list->getNumberNAfterZero(2000)
            + $list->getNumberNAfterZero(3000);

        return new Result($sum);
    }
}
