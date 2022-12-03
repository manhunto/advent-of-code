<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\ClassNotFound;
use App\Solver;
use App\SolverFullyQualifiedClassname;

final class SolutionFactory
{
    /** @var iterable<string, Solver>  */
    private iterable $solutions;

    /**
     * @param Solver[] $solutions
     */
    public function __construct(
        iterable $solutions
    ) {
        foreach ($solutions as $solution) {
            $this->solutions[get_class($solution)] = $solution;
        }
    }

    /**
     * @throws ClassNotFound
     */
    public function create(Date $date): Solver
    {
        $solverNamespace = SolverFullyQualifiedClassname::fromDate($date);

        if (!array_key_exists($className, $this->solutions)) {
            throw ClassNotFound::default($date, $className);
        }

        return new $className;
    }

    /**
     * @return Solver[]
     */
    public function iterate(): iterable
    {
        yield from $this->solutions;
    }
}
