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

        $expression = $this->buildRootExpression($monkeys);

        $equationSolver = new OperationSolver();

        return $equationSolver->resolve($expression);
    }

    private function solveSecondPart(Input $input): string
    {
        $monkeys = $this->parseMonkeys($input);
        $monkeys['root'] = str_replace(' + ', EquationSolver::EQUALS, $monkeys['root']);
        $monkeys['humn'] = EquationSolver::VARIABLE;

        $expression = $this->buildRootExpression($monkeys);

        $equationSolver = new EquationSolver();

        return $equationSolver->resolveEquation($expression);
    }

    private function parseMonkeys(Input $input): array
    {
        $monkeys = [];

        foreach ($input->asArray() as $row) {
            [$monkey, $operation] = explode(': ', $row);

            if (is_numeric($operation) || $monkey === 'root') {
                $monkeys[$monkey] = $operation;
            } else {
                $monkeys[$monkey] = sprintf('(%s)', $operation);
            }
        }

        return $monkeys;
    }

    private function buildRootExpression(array $monkeys): string
    {
        $expression = $monkeys['root'];

        while (preg_match_all('/[a-z]{4}/', $expression, $matches)) {
            foreach ($matches[0] as $subMonkey) {
                $expression = str_replace($subMonkey, $monkeys[$subMonkey], $expression);
            }
        }

        return $expression;
    }
}
