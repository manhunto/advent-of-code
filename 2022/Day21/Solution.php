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
    private const VARIABLE = '$x';
    private const EQUALS = ' == ';

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

        return $this->doEquation($expression);
    }

    private function solveSecondPart(Input $input): string
    {
        $monkeys  = $this->parseMonkeys($input);
        $monkeys['root'] = str_replace('+', '==', $monkeys['root']);
        $monkeys['humn'] = self::VARIABLE;

        $expression = $this->buildRootExpression($monkeys);

        $expression = $this->doEquationIfPossible($expression);

        [$left, $right] = explode(self::EQUALS, $expression);

        $x = null;

        while ($x === null) {
            $left = $this->trimBrackets($left);

            if (preg_match('/^\((.*)\) (\/|\+|\*|\/|\-) (\d+)$/', $left, $matches)
                || preg_match('/^(\d+) (\/|\+|\*|\/|\-) \((.*)\)$/', $left, $matches)) {

                $number = is_numeric($matches[1]) ? $matches[1] : $matches[3];
                $equation = is_numeric($matches[1]) ? $matches[3] : $matches[1];

                $operation = $matches[2];

                $right = $this->calculate($operation, $number, $right);

                $left = $equation;

            } elseif (preg_match('/^(\$x) (\/|\+|\*|\/|\-) (\d+)$/', $left, $matches)
                || preg_match('/^(\d+) (\/|\+|\*|\/|\-) (\$x)$/', $left, $matches)
            ) {
                $number = is_numeric($matches[1]) ? $matches[1] : $matches[3];
                $operation = $matches[2];

                $x = $this->calculate($operation, $number, $right);
            }
        }

        return $x;
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

    private function doEquation(string $equation): int|float
    {
        return eval('return ' . $equation . ';');
    }

    private function trimBrackets(string $expression): string
    {
        if (preg_match('/^\((.*)\)$/', $expression, $matches)) {
            return $matches[1];
        }

        return $expression;
    }

    private function doEquationIfPossible(string $expression): string
    {
        [$left, $right] = explode(self::EQUALS, $expression);

        if (!str_contains($left, self::VARIABLE)) {
            $left = $this->doEquation($left);
        }

        if (!str_contains($right, self::VARIABLE)) {
            $right = $this->doEquation($right);
        }

        $expression = $left . self::EQUALS . $right;

        while (preg_match('/\(\d+ (\+|\-|\*|\/) \d+\)/', $expression, $matches)) {
            $result = $this->doEquation($matches[0]);
            $expression = str_replace($matches[0], (string) $result, $expression);
        }

        return $expression;
    }

    private function calculate(string $operation, string $number, string $right): string
    {
        return match ($operation) {
            '/' => bcmul($right, $number),
            '+' => bcsub($right, $number),
            '*' => bcdiv($right, $number),
            '-' => bcadd($right, $number),
            default => throw new \LogicException('Unexpected operation `' . $operation . '`'),
        };
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
