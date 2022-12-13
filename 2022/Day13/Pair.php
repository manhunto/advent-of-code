<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day13;

class Pair
{
    private function __construct(
        private readonly array $up,
        private readonly array $down,
    ) {
    }

    public static function parse(string $input): self
    {
        $rows = explode(PHP_EOL, $input);

        $up = json_decode($rows[0], true, 512, JSON_THROW_ON_ERROR);
        $down = json_decode($rows[1], true, 512, JSON_THROW_ON_ERROR);

        return new self($up, $down);
    }

    public function isRightOrder(): bool
    {
        return $this->process($this->up, $this->down);
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
}
