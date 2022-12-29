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
        $dayInput = $input->getOption(self::OPTION_DAY);
        $yearInput = $input->getOption(self::OPTION_YEAR);

        if (!$dayInput && !$yearInput) {
            return $this->dateFactory->createForToday();
        }

        $dateTime = new \DateTimeImmutable();

        if ($dayInput) {
            [, $month, $year] = explode('/', $dateTime->format('j/n/Y'));

            $dateTime = $dateTime->setDate((int) $year, (int) $month, (int) $dayInput);
        }

        if ($yearInput) {
            [$day, $month] = explode('/', $dateTime->format('j/n/Y'));

            $dateTime = $dateTime->setDate((int) $yearInput, (int) $month, (int) $day);
        }

        return Date::fromDateTime($dateTime);
    }
}
