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
        private array $items,
        private readonly \Closure $operation,
        public readonly int $divisible,
        private readonly int $monkeyNameThrowIfTrue,
        private readonly int $monkeyNameThrowIfFalse,
    ) {
    }

    /**
     * @param Monkey[] $monkeys
     */
    public function inspectItems(array $monkeys, callable $reduceWorryLevel): void
    {
        foreach ($this->items as $item) {
            $this->inspect($item);

            $reduceWorryLevel($item);

            $throwToMonkeyName = $this->test($item);

            $throwToMonkey = $monkeys[$throwToMonkeyName];
            $throwToMonkey->catch($item);
        }

        $this->items = [];
    }

    private function inspect(Item $item): void
    {
        ($this->operation)($item);

        $this->inspectedItemsCount++;
    }

    private function test(Item $item): int
    {
        return $item->isDivisibleBy($this->divisible) ? $this->monkeyNameThrowIfTrue : $this->monkeyNameThrowIfFalse;
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
