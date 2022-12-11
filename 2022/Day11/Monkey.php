<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day11;

class Monkey
{
    private int $inspectedItemsCount = 0;

    /**
     * @param Item[] $items
     */
    public function __construct(
        private int      $name,
        private array    $items,
        private \Closure $operation,
        private int      $divisible,
        private int      $monkeyNameThrowIfTrue,
        private int      $monkeyNameThrowIfFalse,
    )
    {
    }

    /**
     * @return Item[]
     */
    public function items(): array
    {
        return $this->items;
    }

    private function inspect(Item $item): void
    {
        ($this->operation)($item);

        $item->divideByThree();

        $this->inspectedItemsCount++;
    }

    private function test(Item $item): int
    {
        $result = $item->getWorryLevel() % $this->divisible === 0;

        return $result ? $this->monkeyNameThrowIfTrue : $this->monkeyNameThrowIfFalse;
    }

    public function inspectItems(array $monkeys): void
    {
        $items = $this->items();

        foreach ($items as $item) {
            $this->inspect($item);

            $throwToMonkey = $this->test($item);

            /** @var Monkey $throwTo */
            $throwTo = $monkeys[$throwToMonkey];
            $this->throw($throwTo, $item);
        }
    }

    private function throw(Monkey $throwTo, Item $item): void
    {
        $itemIndex = array_search($item, $this->items, true);
        unset($this->items[$itemIndex]);
        $this->items = array_values($this->items);

        $throwTo->catch($item);
    }

    private function catch(Item $item): void
    {
        $this->items[] = $item;
    }

    public function getInspectedItemsCount(): int
    {
        return $this->inspectedItemsCount;
    }
}
