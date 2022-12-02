<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\ClassNotFound;
use App\Solver;

final class SolutionFactory
{
    /**
     * @throws ClassNotFound
     */
    public function create(Date $date): Solver
    {
        $className = sprintf("\AdventOfCode%s\Day%s\Solution", $date->year, $date->day);

        if (!class_exists($className)) {
            throw ClassNotFound::default($date, $className);
        }

        return new $className;
    }
}
