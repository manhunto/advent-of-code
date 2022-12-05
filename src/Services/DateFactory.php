<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\DateCannotBeGeneratedForToday;

final class DateFactory
{
    /**
     * @throws DateCannotBeGeneratedForToday
     */
    public function createForToday(): Date
    {
        $today = new \DateTimeImmutable();

        if (!$this->isDateDuringAOC($today)) {
            throw new DateCannotBeGeneratedForToday('Generating date for today is only available during the advent of code.');
        }

        return Date::fromDateTime($today);
    }

    private function isDateDuringAOC(\DateTimeImmutable $date): bool
    {
        $startDate = \DateTimeImmutable::createFromFormat('d-m', '05-12')->setTime(0,0);
        $endDate = \DateTimeImmutable::createFromFormat('d-m', '25-12')->setTime(23, 59, 59);

        return $date >= $startDate && $date <= $endDate;
    }
}
