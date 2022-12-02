<?php

declare(strict_types=1);

namespace App;

interface Solver
{
    public function solve(Input $input): Result;
}
