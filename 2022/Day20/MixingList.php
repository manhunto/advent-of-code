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
            $numbers = Collection::create($this->currentOrder)
                ->unsetKeys([$positionInCurrentOrder]);

            $newIndex = ($positionInCurrentOrder + $value) % $numbers->count();

            $this->currentOrder = $numbers
                ->insertItemAtIndex($number, $newIndex)
                ->values()
                ->toArray();
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
