<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day13;

class Packet
{
    public function __construct(
        private readonly array $items,
    ) {
    }

    public static function parse(string $input): self
    {
        return new self(json_decode($input, true, 512, JSON_THROW_ON_ERROR));
    }

    public function encode(): string
    {
        return json_encode($this->items, JSON_THROW_ON_ERROR);
    }

    public function isLowerThan(Packet $down): bool
    {
        return $this->isLowerThanForArrays($this->items, $down->items);
    }

    /**
     * @return bool|null null - if items are equals
     */
    private function isLowerThanForArrays(array $up, array $down): ?bool
    {
        foreach ($up as $index => $A) {
            $B = $down[$index] ?? null;

            if ($B === null) { // If the right list runs out of items first, the inputs are not in the right order.
                return false;
            }

            $isAArray = is_array($A);
            $isBArray = is_array($B);

            if (!$isAArray && !$isBArray && $A !== $B) {
                return $A < $B;
            }

            if ($isAArray || $isBArray) {
                $A = $isAArray ? $A : [$A];
                $B = $isBArray ? $B : [$B];

                $result = $this->isLowerThanForArrays($A, $B);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        if (count($up) < count($down)) { // If the left list runs out of items first, the inputs are in the right order.
            return true;
        }

        return null;
    }
}
