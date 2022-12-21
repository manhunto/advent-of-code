<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day20;

use App\Utils\Collection;

class MixingList
{
    /** @var Number[] */
    private readonly array $initialNumbers;
    private array $currentOrder;
    private int $currentMove;

    public function __construct(
        array $numbers,
        int $currentMove = 1,
    )
    {
        $newNumbers = [];
        foreach ($numbers as $number) {
            $newNumbers[] = new Number((int) $number);
        }

        $this->initialNumbers = $newNumbers;
        $this->currentOrder = $this->initialNumbers;
        $this->currentMove = $currentMove;
    }

    public function asArrayOfInt(): array
    {
        return array_map(static fn(Number $number): int => $number->value, $this->currentOrder);
    }

    public function move(): void
    {
        $originIndex = $this->currentMove - 1;

        $number = $this->initialNumbers[$originIndex];
        $positionInCurrentOrder = $this->getIndexInCurrentOrder($number);

        $value = $number->value;

        if ($value !== 0) {
            $newIndex = $positionInCurrentOrder + $value % count($this->currentOrder);
            if ($value > 0) {
                $newIndex = ($positionInCurrentOrder + $value + 1) % count($this->currentOrder);
            } else {
                $newIndex = $positionInCurrentOrder + $value;

                if ($newIndex <= 0) {
                    $diff = $newIndex % count($this->currentOrder);

                    $newIndex = count($this->currentOrder) + $diff;
                }
            }

            $this->replaceNumberInCurrent($number, $newIndex);
        }

        $this->currentMove++;
    }

    private function getIndexInCurrentOrder(Number $searchFor): int
    {
        $index = Collection::create($this->currentOrder)
            ->searchKey(static fn (Number $number): bool => $searchFor->isTheSame($number));

        if ($index === null) {
            throw new \LogicException('Unable to find index for number ' . $searchFor->value);
        }

        return $index;
    }

    private function replaceNumberInCurrent(Number $number, int $newItemIndex): void
    {
        $this->currentOrder = Collection::create($this->currentOrder)
            ->insertItemAtIndex($number, $newItemIndex)
            ->unset(static fn (Number $other, int $index): bool =>
                $other->isTheSame($number) && $index !== $newItemIndex
            )
            ->values()
            ->toArray();
    }

    public function getNumberNAfterZero(int $value): int
    {
        $zeroIndex = Collection::create($this->currentOrder)
            ->searchKey(static fn (Number $number): bool => $number->value === 0);


        if ($zeroIndex === null) {
            throw new \LogicException('Cannot found 0 in list');
        }

        $nextIndex = ($zeroIndex + $value) % count($this->currentOrder);
        $nextNumber = $this->currentOrder[$nextIndex] ?? null;

        if ($nextNumber === null) {
            throw new \LogicException('There is no number at index ' . $nextIndex);
        }


        return $nextNumber->value;
    }

    public function mix(): void
    {
        $move = 1;

        do {
            $this->move();
            $move++;
        } while ($move <= count($this->currentOrder));

        $this->currentMove = 1;
    }
}
