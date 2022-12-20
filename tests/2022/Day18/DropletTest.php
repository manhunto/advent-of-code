<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day18;

use App\Utils\Point3D;
use PHPUnit\Framework\TestCase;

class DropletTest extends TestCase
{
    public function testName()
    {
        $droplet = new Droplet([
            new Point3D(1, 0, 0),
            new Point3d(0, 1, 0),
            new Point3d(0, 0, 1),
            new Point3D(-1, 0, 0),
            new Point3d(0, -1, 0),
            new Point3d(0, 0, -1)
        ]);

        self::assertSame(36, $droplet->calculateWholeSurface());
        self::assertSame(30, $droplet->calculateExteriorSurface());

    }

}
