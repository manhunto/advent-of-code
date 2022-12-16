<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Utils\Point;
use App\Utils\Range;

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
        $range = $this->getRangeOnLine($y);

        if ($range === null) {
            return [];
        }

        return range($range->from, $range->to);
    }

    public function getRangeOnLine(int $y): ?Range
    {
        $originDistance = $this->getDistanceInManhattanGeometry();

        $diffInY = abs($this->sensorLocation->y - $y);

        $from = $this->sensorLocation->x - $originDistance + $diffInY;
        $to = $this->sensorLocation->x + $originDistance - $diffInY;

        try {
            return new Range($from, $to);
        } catch (\LogicException) {
            return null;
        }
    }

    private function getDistanceInManhattanGeometry(): int
    {
        return $this->sensorLocation->distanceInManhattanGeometry($this->beaconLocation);
    }
}
