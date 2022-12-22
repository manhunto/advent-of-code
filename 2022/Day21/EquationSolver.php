<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day21;

/**
 * It is only resolve equation when variable is on left side
 * All operations have to be in brackets
 */
class EquationSolver
{
    public const EQUALS = ' == ';
    public const VARIABLE = 'x';

    private OperationSolver $operationSolver;

    public function __construct()
    {
        $this->operationSolver = new OperationSolver();
    }

    public function resolveEquation(string $expression): string
    {
        $expression = $this->resolvePossibleOperations($expression);

        [$left, $right] = explode(self::EQUALS, $expression);

        $x = null;

        while ($x === null) {
            $left = $this->trimBrackets($left);

            if (
                preg_match('/^\((?<left>.*)\) (?<operation>\/|\+|\*|\/|\-) (?<right>\d+)$/', $left, $matches)
                || preg_match('/^(?<left>\d+) (?<operation>\/|\+|\*|\/|\-) \((?<right>.*)\)$/', $left, $matches)
                || preg_match('/^(?<left>x|\d+) (?<operation>\/|\+|\*|\/|\-) (?<right>\d+|x)$/', $left, $matches)
            ) {
                [$left, $right] = $this->moveAndCalculateNumbersToRight($matches, $right);

                if ($left === self::VARIABLE) {
                    $x = $right;
                }
            }
        }

        return $x;
    }

    private function resolvePossibleOperations(string $equation): string
    {
        [$left, $right] = explode(self::EQUALS, $equation);

        if (!str_contains($left, self::VARIABLE)) {
            $left = $this->resolveOperation($left);
        }

        if (!str_contains($right, self::VARIABLE)) {
            $right = $this->resolveOperation($right);
        }

        $equation = $left . self::EQUALS . $right;

        while (preg_match('/\(\d+ (\+|\-|\*|\/) \d+\)/', $equation, $matches)) {
            $result = $this->resolveOperation($matches[0]);
            $equation = str_replace($matches[0], (string) $result, $equation);
        }

        return $equation;
    }

    private function resolveOperation(string $operation)
    {
        return $this->operationSolver->resolve($operation);
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

    private function trimBrackets(string $expression): string
    {
        if (preg_match('/^\((.*)\)$/', $expression, $matches)) {
            return $matches[1];
        }

        return $expression;
    }

    private function moveAndCalculateNumbersToRight(array $subExpressionMatches, string $right): array
    {
        $leftSide = $subExpressionMatches['left'];
        $rightSide = $subExpressionMatches['right'];
        $operation = $subExpressionMatches['operation'];

        if (is_numeric($leftSide) && in_array($operation, ['/', '-'])) {
            if ($operation === '/') {
                $operation = '*';
            } else if ($operation === '-') {
                $operation = '+';
            }

            $equation = $rightSide;
            $number = $right;
            $right = $leftSide;
        } else {
            $number = is_numeric($leftSide) ? $leftSide : $rightSide;
            $equation = is_numeric($leftSide) ? $rightSide : $leftSide;
        }

        $left = $equation;
        $right = $this->calculate($operation, $number, $right);

        return [$left, $right];
    }
}
