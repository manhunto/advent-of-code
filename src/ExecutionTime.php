<?php

declare(strict_types=1);

namespace App;

class ExecutionTime
{
    public function __construct(
        private readonly float $timeInMicroSeconds
    ) {
    }

    public function getFormattedInMiliSeconds(): string
    {
        return number_format($this->timeInMicroSeconds * 1000, 3) . ' ms';
    }
}
