<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;

final class DateFactory
{
    public function createForToday(): Date
    {
        $today = new \DateTimeImmutable();

        return Date::createForDateTime($today);
    }
}
