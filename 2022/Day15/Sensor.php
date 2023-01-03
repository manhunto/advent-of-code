<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day15;

use App\Utils\Location;
use App\Utils\Range;

class Sensor
{
    public function __construct(
        private readonly Location $sensorLocation,
        public readonly Location  $beaconLocation,
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

    /**
     * @return iterable<Location>
     */
    public function getPointsOnBorderPlusOne(Range $inRange): iterable
    {
        $yRange = $this->getRangeOnVertical()
            ->expandBoundariesBy(1);

        if ($yRange->collide($inRange) === false) {
            return;
        }

        $yRange = $yRange->intersect($inRange);

        foreach ($yRange->getItems() as $y) {
            $xRange = $this->getRangeOnLine($y);

            if ($xRange === null) {
                $x = $this->sensorLocation->x;

                if ($inRange->isNumberInRange($x)) {
                    yield new Location($x, $y);
                }

                continue;
            }

            $xRange = $xRange->expandBoundariesBy(1);

            if ($xRange->collide($inRange) === false) {
                continue;
            }

            if ($inRange->isNumberInRange($xRange->from)) {
                yield new Location($xRange->from, $y);
            }

            if ($inRange->isNumberInRange($xRange->to)) {
                yield new Location($xRange->to, $y);
            }
        }
    }

    public function canBeReachedBySensor(Location $location): bool
    {
        $toBeacon = $this->getDistanceInManhattanGeometry();
        $toLocation = $this->sensorLocation->distanceInManhattanGeometry($location);

        return $toBeacon >= $toLocation;
    }

    private function getRangeOnVertical(): Range
    {
        $radius = $this->getDistanceInManhattanGeometry();

        $from = $this->sensorLocation->y - $radius;
        $to = $this->sensorLocation->y + $radius;

        return new Range($from, $to);
    }

    private function getDistanceInManhattanGeometry(): int
    {
        return $this->sensorLocation->distanceInManhattanGeometry($this->beaconLocation);
    }
}
