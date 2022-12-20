<?php

declare(strict_types=1);

namespace App\Utils;

class Space3DBoundary
{
    private function __construct(
        private readonly Range $rangeX,
        private readonly Range $rangeY,
        private readonly Range $rangeZ,
    ) {
    }

    public static function createForPoints(Point3D ...$points): self
    {
        $array = [...$points];
        $firstPoint = array_shift($array);

        $rangeX = Range::createForPoint($firstPoint->x);
        $rangeY = Range::createForPoint($firstPoint->y);
        $rangeZ = Range::createForPoint($firstPoint->z);

        foreach ($array as $point) {
            $rangeX = $rangeX->expandTo($point->x);
            $rangeY = $rangeY->expandTo($point->y);
            $rangeZ = $rangeZ->expandTo($point->z);
        }

        return new self($rangeX, $rangeY, $rangeZ);
    }

    public function getLowestPossiblePoint(): Point3D
    {
        return new Point3D(
            $this->rangeX->from,
            $this->rangeY->from,
            $this->rangeZ->from,
        );
    }

    public function isPointInside(Point3D $point): bool
    {
        return $this->rangeX->isNumberInRange($point->x)
            && $this->rangeY->isNumberInRange($point->y)
            && $this->rangeZ->isNumberInRange($point->z);
    }

    public function forEachPointInBoundary(callable $callable): void
    {
        foreach ($this->rangeX->getItems() as $x) {
            foreach ($this->rangeY->getItems() as $y) {
                foreach ($this->rangeZ->getItems() as $z) {
                    $point = new Point3D($x, $y, $z);

                    $callable($point, $this);
                }
            }
        }
    }

    public function expandBy(int $value): self
    {
        return new self(
            $this->rangeX->expandBoundariesBy($value),
            $this->rangeY->expandBoundariesBy($value),
            $this->rangeZ->expandBoundariesBy($value),
        );
    }
}
