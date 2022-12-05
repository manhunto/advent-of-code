<?php

declare(strict_types=1);

namespace App\Services;

use App\PuzzleLink;
use App\SolutionDescription;
use App\SolutionAttribute;
use App\Solver;
use App\SolverFullyQualifiedClassname;

final class SolutionsDescriptor
{
    public function getDescription(Solver $solver): SolutionDescription
    {
        $reflection = new \ReflectionClass($solver);
        $attributes = $reflection->getAttributes(SolutionAttribute::class);

        $arguments = [];
        if (!empty($attributes)) {
            $arguments = $attributes[0]->getArguments();
        }

        $fqn = SolverFullyQualifiedClassname::fromObject($solver);

        return new SolutionDescription(
            $fqn->getDate(),
            $arguments['name'] ?? null,
            (string) PuzzleLink::fromDate($fqn->getDate())
        );
    }
}
