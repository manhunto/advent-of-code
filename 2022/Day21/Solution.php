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
        $firstPartResult = $this->solveFirstPart($input);
        $secondPartResult = $this->solveSecondPart($input);

        return new Result($firstPartResult, $secondPartResult);
    }

    private function solveFirstPart(Input $input): int
    {
        $monkeys = $this->parseMonkeys($input);

        $expression = $monkeys['root'];

        while (preg_match_all('/[a-z]{4}/', $expression, $matches)) {
            foreach ($matches[0] as $subMonkey) {
                $expression = str_replace($subMonkey, $monkeys[$subMonkey], $expression);
            }
        }

        return eval('return ' . $expression . ';');
    }

    private function solveSecondPart(Input $input): int
    {
        $monkeys  = $this->parseMonkeys($input);
        $monkeys['root'] = str_replace('+', '==', $monkeys['root']);
        $monkeys['humn'] = '$x';

        $expression = $monkeys['root'];

        while (preg_match_all('/[a-z]{4}/', $expression, $matches)) {
            foreach ($matches[0] as $subMonkey) {
                $expression = str_replace($subMonkey, $monkeys[$subMonkey], $expression);
            }
        }

        foreach (range(-10_000, 10_000, 1) as $number) {
            $newExpression = sprintf('$x = %s; return %s;', $number, $expression);

            $result = eval($newExpression);

            if ($result) {
                return $number;
            }
        }

        throw new \LogicException('There is no correct answer');
    }

    private function parseMonkeys(Input $input): array
    {
        $monkeys = [];

        foreach ($input->asArray() as $row) {
            [$monkey, $operation] = explode(': ', $row);

            if (is_numeric($operation)) {
                $monkeys[$monkey] = $operation;
            } else {
                $monkeys[$monkey] = '(' . $operation . ')';
            }
        }

        return $monkeys;
    }
}
