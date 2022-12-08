<?php

declare(strict_types=1);

namespace App;

class Stopwatch
{
    private float $start = 0;

    public function start(): void
    {
        $this->start = microtime(true);
    }

    public function endInMiliSeconds(): ExecutionTime
    {
        $end = microtime(true) - $this->start;

        $this->start = 0;

        return new ExecutionTime($end);
    }
}
