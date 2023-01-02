<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day25;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Output\Console as C;

#[SolutionAttribute(
    name: 'Full of Hot Air',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $converter = new SNAFUConverter();

        $decimalSum = Collection::create($input->asArray())
            ->forEach(static fn (string $snafu): string => $converter->toDecimal($snafu))
            ->sum();

        return new Result($converter->toSNAFU((string) $decimalSum)); // todo convert to snafu
    }
}
