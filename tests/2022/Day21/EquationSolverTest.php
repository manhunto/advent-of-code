<?php

declare(strict_types=1);

namespace Tests\AdventOfCode2022\Day21;

use AdventOfCode2022\Day21\EquationSolver;
use PHPUnit\Framework\TestCase;

class EquationSolverTest extends TestCase
{
    /**
     * @dataProvider equationData
     */
    public function testResolveEquation(string $equation, string $expected): void
    {
        $solver = new EquationSolver();

        $result = $solver->resolveEquation($equation);

        self::assertSame($expected, $result);
    }

    public function equationData(): ?\Generator
    {
        yield [
            'x / 2 == 16',
            '32'
        ];

        yield [
            '(x - 2) / 2 == 16',
            '34'
        ];

        yield [
            '16 / x == 4',
            '4'
        ];

        yield [
            '16 / (x + 2) == 4',
            '2'
        ];

        yield [
            'x - 10 == 4',
            '14'
        ];

        yield [
            '(x + 3) - 10 == 4',
            '11'
        ];

        yield [
            '14 - x == 4',
            '10'
        ];

        yield [
            '14 - (x - 3) == 4',
            '13'
        ];
    }

}
