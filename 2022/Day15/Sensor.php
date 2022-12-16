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

    public function getRangeOnLine(int $y): ?Range
    {
        $radius = $this->getDistanceInManhattanGeometry();

        $diffInY = abs($this->sensorLocation->y - $y);

        $from = $this->sensorLocation->x - $radius + $diffInY;
        $to = $this->sensorLocation->x + $radius - $diffInY;

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
