<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Date;

class ClassNotFound extends \Exception
{
    public static function default(Date $date, string $className): self
    {
        return new self(
            sprintf(
                'There is no solution for day %s in %s year. Class %s does not exist.',
                $date->day, $date->getYearAsString(), $className
            )
        );
    }
}
