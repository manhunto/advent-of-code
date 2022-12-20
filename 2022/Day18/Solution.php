<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day18;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;
use App\Utils\Collection;
use App\Utils\Point3D;

#[SolutionAttribute(
    name: 'Boiling Boulders',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        $points3D = Collection::create($input->asArray())
            ->forEach(static function (string $row): Point3D {
                [$x, $y, $z] = explode(',', $row);

                return new Point3D((int) $x, (int) $y, (int) $z);
            })
            ->toArray();

        $droplet = new Droplet($points3D);
        $surfaceFirstPart = $droplet->calculateWholeSurface();
        $surfaceSecondPart = $droplet->calculateExteriorSurface();

        return new Result($surfaceFirstPart, $surfaceSecondPart);
    }
}
