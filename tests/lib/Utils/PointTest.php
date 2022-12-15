<?php

declare(strict_types=1);

namespace Tests\Lib\Utils;

use App\Utils\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testDistanceInManhattanGeometry(): void
    {
        $point = new Point(0, 0);
        $point2 = new Point(2, 2);

        self::assertSame(4, $point->distanceInManhattanGeometry($point2));
    }

}
