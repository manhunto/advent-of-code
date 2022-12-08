<?php

declare(strict_types=1);

namespace App;

final class SolverResult
{
    public function __construct(
        private readonly Result $currentResult,
        private readonly Result $expectedResult,
        private readonly ExecutionTime $executionTime,
    ) {
    }

    public function isResolvedCorrectly(): bool
    {
        return $this->currentResult->equals($this->expectedResult);
    }

    public function getCurrentResult(): Result
    {
        return $this->currentResult;
    }

    public function getExpectedResult(): Result
    {
        return $this->expectedResult;
    }

    public function getExecutionTimeInMiliSeconds(): string
    {
        return $this->executionTime->getFormattedInMiliSeconds();
    }
}
