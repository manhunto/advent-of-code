<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day20;

use App\Utils\Collection;

class MixingList
{
    private const GROOVE_COORDINATES = [1000, 2000, 3000];

    private readonly Collection $initialOrder;
    private Collection $currentOrder;
    private int $currentMove;

    public function __construct(
        array $numbers,
        int $encryptionKey = 1,
        int $currentMove = 1,
    ) {
        $this->initialOrder = Collection::create(array_map(
            static fn ($value): Number => new Number((int) $value * $encryptionKey),
            $numbers
        ));

        $this->currentOrder = $this->initialOrder->clone();
        $this->currentMove = $currentMove;
    }

    public function asArrayOfInt(): array
    {
        return $this->currentOrder
            ->forEach(static fn(Number $number): int => $number->value)
            ->toArray();
    }

    public function move(): void
    {
        /** @var Number $number */
        $number = $this->initialOrder->get($this->currentMove - 1);
        $positionInCurrentOrder = $this->getIndexInCurrentOrder($number);

        $value = $number->value;

        if ($value !== 0) {
            $numbers = $this->currentOrder
                ->unsetKeys([$positionInCurrentOrder]);

            $newIndex = ($positionInCurrentOrder + $value) % $numbers->count();

            $this->currentOrder = $numbers
                ->insertItemAtIndex($number, $newIndex);
        }

        $this->currentMove++;
    }

    private function getIndexInCurrentOrder(Number $searchFor): int
    {
        $index = $this->currentOrder
            ->searchKey(static fn (Number $number): bool => $searchFor->isTheSame($number));

        if ($index === null) {
            throw new \LogicException('Unable to find index for number ' . $searchFor->value);
        }

        return $index;
    }

    public function mix(): void
    {
        $move = 1;

        do {
            $this->move();
            $move++;
        } while ($move <= $this->initialOrder->count());

        $this->currentMove = 1;
    }

    public function mixTenTimes(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->mix();
        }
    }

    public function getGrooveCoordinatesValuesSum(): int
    {
        $zeroIndex = $this->currentOrder
            ->searchKey(static fn (Number $number): bool => $number->value === 0);

        $normalizedKeys = Collection::create(self::GROOVE_COORDINATES)
            ->forEach(fn (int $value): int => ($zeroIndex + $value) % $this->currentOrder->count())
            ->toArray();

        return $this->currentOrder
            ->filterKeysByValue(...$normalizedKeys)
            ->forEach(static fn (Number $number): int => $number->value)
            ->sum();
    }
}
