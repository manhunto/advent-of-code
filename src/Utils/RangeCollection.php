<?php

declare(strict_types=1);

namespace App\Utils;

class RangeCollection
{
    /** @var Range[] */
    private array $ranges = [];

    public function union(Range ...$range): void
    {
        $ranges = [...$this->ranges, ...$range];

        while (true) {
            $ranges = array_values($ranges);

            /**
             * @var int $index
             * @var Range $rangeToTest
             */
            foreach ($ranges as $index => $rangeToTest) {
                /**
                 * @var int $suspectIndex
                 * @var Range $suspectToSumRange
                 */
                foreach ($ranges as $suspectIndex => $suspectToSumRange) {
                    if ($index === $suspectIndex) {
                        continue;
                    }

                    try {
                        $sum = $rangeToTest->union($suspectToSumRange);

                        unset($ranges[$index], $ranges[$suspectIndex]);
                        $ranges[] = $sum;
                        continue 3;
                    } catch (\LogicException) {
                    }
                }
            }

            break;
        }

        $this->ranges = $this->sort(...$ranges);
    }

    public function intersect(Range $range): void
    {
        $ranges = array_values($this->ranges);

        /**
         * @var int $suspectIndex
         * @var Range $suspectToSumRange
         */
        foreach ($ranges as $suspectIndex => $suspectToSumRange) {
            try {
                $intersected = $range->intersect($suspectToSumRange);
                $ranges[$suspectIndex] = $intersected;
            } catch (\LogicException) {
            }
        }

        $this->ranges = $this->sort(...$ranges);
    }


    /**
     * @return Range[]
     */
    public function getRanges(): array
    {
        return $this->ranges;
    }

    /**
     * @return Range[]
     */
    public function getGaps(): array
    {
        $ranges = $this->ranges;

        if (count($ranges) <= 1) {
            return [];
        }

        $prev = array_shift($ranges);
        $gaps = [];

        foreach ($ranges as $range) {
            $gaps[] = new Range($prev->to + 1, $range->from - 1);
            $prev = $range;
        }

        return $gaps;
    }

    /**
     * @return Range[]
     */
    private function sort(Range ...$ranges): array
    {
        uasort($ranges, static function (Range $A, Range $B) {
            return $A->from <=> $B->from;
        });

        return array_values($ranges);
    }
}
