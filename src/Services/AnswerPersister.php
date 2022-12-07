<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Result;

class AnswerPersister
{
    public function saveAnswer(Date $date, Result $result): void
    {
        if ($result->hasAtLeasOneAnswer()) {
            file_put_contents(
                sprintf(__DIR__ . '/../../%s/Day%s/puzzle.out', $date->getYearAsString(), $date->day),
                implode(PHP_EOL, $result->getAsArray()) . PHP_EOL,
            );
        }
    }
}
