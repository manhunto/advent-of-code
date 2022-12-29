<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day23;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Location;
use App\Utils\Map;
use App\Utils\Output\Console;
use App\Utils\Output\Console as C;

#[SolutionAttribute(
    name: 'Unstable Diffusion',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $elfMoves = ElfMoves::fromInput($input);

        $elfMoves->moveNTimes(10);

        $emptyGround = $elfMoves->countEmptyGround();
    
        return new Result($emptyGround);
    }
}
