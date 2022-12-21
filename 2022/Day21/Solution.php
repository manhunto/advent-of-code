<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day21;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'Monkey Math',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $monkeys = [];

        foreach ($input->asArray() as $row) {
            [$monkey, $operation] = explode(': ', $row);

            $monkeys[$monkey] = '(' . $operation . ')';
        }

        $expression = $monkeys['root'];

        while (preg_match_all('/[a-z]{4}/', $expression, $matches)) {
            foreach ($matches[0] as $subMonkey) {
                $expression = str_replace($subMonkey, $monkeys[$subMonkey], $expression);
            }
        }

        $result = eval('return ' . $expression . ';');
    
        return new Result($result);
    }
}
