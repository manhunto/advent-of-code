<?php

declare(strict_types=1);

namespace App\Commands\Helpers;

use App\Date;
use App\Exceptions\DateCannotBeGeneratedForToday;
use App\Services\DateFactory;
use Symfony\Component\Console\Input\InputInterface;

final class DateInputHelper
{
    public const OPTION_DAY = 'day';
    public const OPTION_YEAR = 'year';

    public function __construct(
        private readonly DateFactory $dateFactory
    ) {
    }

    /**
     * @throws DateCannotBeGeneratedForToday
     */
    public function prepareDate(InputInterface $input): Date
    {
        $date = $this->dateFactory->createForToday();

        if ($dayInput = $input->getOption(self::OPTION_DAY)) {
            $date = $date->withDay($dayInput);
        }

        if ($yearInput = $input->getOption(self::OPTION_YEAR)) {
            $date = $date->withYear($yearInput);
        }

        return $date;
    }
}
