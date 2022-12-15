<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Utils\Point;

class Sensor
{
    public function __construct(
        private readonly Point $sensorLocation,
        private readonly Point $beaconLocation,
    ) {
    }

    public function isInRange(Point $point): bool
    {
        $distanceToBeacon = $this->sensorLocation->distanceInManhattanGeometry($this->beaconLocation);
        $distanceToPoint = $this->sensorLocation->distanceInManhattanGeometry($point);

        return $distanceToBeacon > $distanceToPoint;
    }

    public function isBeaconPosition(Point $point): bool
    {
        return $this->beaconLocation->equals($point);
    }
}
