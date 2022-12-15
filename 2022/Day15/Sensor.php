<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Utils\Point;

class Sensor
{
    public function __construct(
        private readonly Point $sensorLocation,
        public readonly Point $beaconLocation,
    ) {
    }

    public function isInRange(Point $point): bool
    {
        $distanceToBeacon = $this->getDistanceInManhattanGeometry();
        $distanceToPoint = $this->sensorLocation->distanceInManhattanGeometry($point);

        return $distanceToBeacon > $distanceToPoint;
    }

    public function isBeaconPosition(Point $point): bool
    {
        return $this->beaconLocation->equals($point);
    }

    public function getCoveredXPositionsInY(int $y): array
    {
        $originDistance = $this->getDistanceInManhattanGeometry();

        $diffInY = abs($this->sensorLocation->y - $y);

        $minX = $this->sensorLocation->x - $originDistance + $diffInY;
        $maxX = $this->sensorLocation->x + $originDistance - $diffInY;

        if ($minX > $maxX) {
            return [];
        }

        return range($minX, $maxX);
    }

    private function getDistanceInManhattanGeometry(): int
    {
        return $this->sensorLocation->distanceInManhattanGeometry($this->beaconLocation);
    }
}
