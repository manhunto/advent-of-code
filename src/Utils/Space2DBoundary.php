<?php

declare(strict_types=1);

namespace App\Utils;

class Space2DBoundary
{
    private function __construct(
        private readonly Range $rangeX,
        private readonly Range $rangeY,
    ) {
    }

    public static function fromPrimitives(int $minX, int $minY, int $maxX, int $maxY): self
    {
        return new self (
            new Range($minX, $maxX),
            new Range($minY, $maxY)
        );
    }

    public static function fromLocations(Location $from, Location $to): self
    {
        return self::fromPrimitives($from->x, $to->x, $from->y, $to->x);
    }

    public static function fromRanges(Range $rangeX, Range $rangeY): self
    {
        return new self($rangeX, $rangeY);
    }

    /**
     * If location is outside boundaries then move to beginning or end of range
     * Example: if location exceed X boundary on end, then it is moved to beginning of X
     */
    public function wrapAroundBoundariesIfRequired(DirectionalLocation $directionalLocation): DirectionalLocation
    {
        $location = $directionalLocation->location;

        if ($this->rangeX->isAfter($location->x)) {
            return $directionalLocation->changeX($this->rangeX->from);
        }

        if ($this->rangeX->isBefore($location->x)) {
            return $directionalLocation->changeX($this->rangeX->to);
        }

        if ($this->rangeY->isBefore($location->y)) {
            return $directionalLocation->changeY($this->rangeY->to);
        }

        if ($this->rangeY->isAfter($location->y)) {
            return $directionalLocation->changeY($this->rangeY->from);
        }

        return $directionalLocation;
    }

    public function isInBoundary(Location $location): bool
    {
        return $this->rangeX->isNumberInRange($location->x) && $this->rangeY->isNumberInRange($location->y);
    }
}
