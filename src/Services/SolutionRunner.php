<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileNotFound;
use App\ExecutionTime;
use App\InputType;
use App\Result;
use App\SolverResult;
use App\Solver;
use App\SolverFullyQualifiedClassname;
use App\Stopwatch;

final class SolutionRunner
{
    public function __construct(
        private readonly FileSystem $fileLoader,
    ) {
    }

    /**
     * @throws FileNotFound
     */
    public function run(Solver $solution, InputType $inputType): SolverResult
    {
        $date = SolverFullyQualifiedClassname::fromObject($solution)->getDate();

        $inputFile = $this->fileLoader->loadInput($date, $inputType);
        $expectedResult = $this->fileLoader->loadExpectedResult($date, $inputType);

        [$result, $executionTime] = $this->runWithBenchmark(fn () => $solution->solve($inputFile));

        return new SolverResult($result, $expectedResult, $executionTime);
    }

    /**
     * @return array{0: Result, 1: ExecutionTime}
     */
    private function runWithBenchmark(callable $func): array
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start();
        $result = $func();
        $executionTime = $stopwatch->endInMiliSeconds();

        return [$result, $executionTime];
    }
}
