<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day21;

class OperationSolver
{
    public function resolve(string $operation)
    {
        return eval('return ' . $operation . ';');
    }
}
