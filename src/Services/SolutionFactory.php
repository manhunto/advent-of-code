<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\ClassNotFound;
use App\Solver;
use App\SolverFullyQualifiedClassname;
use App\Year;

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

        if (!array_key_exists($solverNamespace->getAsString(), $this->solutions)) {
            throw ClassNotFound::default($date, $solverNamespace->getAsString());
        }

        return $this->solutions[$solverNamespace->getAsString()];
    }

    /**
     * @return Solver[]
     */
    public function iterateForYear(Year $year): iterable
    {
        foreach ($this->iterate() as $solver) {
            $solverNamespace = SolverFullyQualifiedClassname::fromObject($solver);

            if ($solverNamespace->getDate()->isYearEquals($year)) {
                yield $solver;
            }
        }
    }

    /**
     * @return Year[]
     */
    public function getAvailableYears(): array
    {
        $years = [];

        foreach ($this->iterate() as $solver) {
            $solverNamespace = SolverFullyQualifiedClassname::fromObject($solver);

            $years[] = $solverNamespace->getDate()->year;
        }

        return array_unique($years);
    }

    /**
     * @return Solver[]
     */
    private function iterate(): iterable
    {
        yield from $this->solutions;
    }
}
