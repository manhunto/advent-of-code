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

    public function isLowerThan(Packet $down): bool
    {
        return $this->process($this->items, $down->items);
    }

    private function process(array|int $up, array|int $down): ?bool
    {
        $countA = count($up);
        $countB = count($down);

        foreach ($up as $index => $A) {

            $B = $down[$index] ?? null;

            if ($B === null) {
                return false; // If the right list runs out of items first, the inputs are not in the right order.
            }

            $isANumeric = is_numeric($A);
            $isBNumeric = is_numeric($B);

            if (!$isANumeric && !$isBNumeric) {
                $result = $this->process($A, $B);
                if ($result !== null) {
                    return $result;
                }
            } elseif(!$isANumeric && $isBNumeric) {
                $result = $this->process($A, [$B]);
                if ($result !== null) {
                    return $result;
                }
            } elseif($isANumeric && !$isBNumeric) {
                $result = $this->process([$A], $B);
                if ($result !== null) {
                    return $result;
                }
            } else {
                if ($A < $B) { // If the left integer is lower than the right integer, the inputs are in the right order.
                    return true;
                }

                if ($A > $B) { // If the left integer is higher than the right integer, the inputs are not in the right order.
                    return false;
                }
            }
        }

        if ($countA > $countB) {
            return false;
        }

        if ($countA < $countB) {
            return true;
        }

        return null; // If the left list runs out of items first, the inputs are in the right order.
    }

    public function encode(): string
    {
        return json_encode($this->items, JSON_THROW_ON_ERROR);
    }
}
