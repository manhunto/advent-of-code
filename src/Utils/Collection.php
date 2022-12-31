<?php

declare(strict_types=1);

namespace App\Utils;

final class Collection
{
    public function __construct(
        private readonly array $items
    ) {
    }

    public static function withExplode(string $separator, string $string): self
    {
        return self::create(explode($separator, $string));
    }

    public static function create(array $array = []): self
    {
        return new self($array);
    }

    private static function empty(): self
    {
        return self::create([]);
    }

    public function indicesStartAtOne(): self
    {
        return self::create(self::withOneAsFirstIndex($this->items));
    }

    public static function withOneAsFirstIndex(array $array): array
    {
        return array_combine(
            keys: range(1, count($array)),
            values: array_values($array)
        );
    }

    public function forEach(callable $callback): self
    {
        return self::create(array_map($callback, $this->items));
    }

    public function uasort(callable $callback): self
    {
        $items = $this->items;

        uasort($items, $callback);

        return self::create($items);
    }

    public function getIndex(mixed $needle): bool|int|string
    {
        return array_search($needle, $this->items, true);
    }

    public function getIndices(callable $callback): self
    {
        $indices = [];

        foreach ($this->items as $index => $item) {
            if ($callback($item, $index)) {
                $indices[] = $index;
            }
        }

        return self::create($indices);
    }

    public function sum(): float|int
    {
        return array_sum($this->items);
    }

    public function multiply(): float|int
    {
        return array_reduce($this->items, static fn($carry, mixed $item) => $carry * $item, 1);
    }

    public function getIndicesForItemsInArray(array $haystack): self
    {
        return $this->getIndices(static fn(string $encodedPacket) => in_array($encodedPacket, $haystack, true));
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function diff(array ...$arrays): self
    {
        return self::create(array_diff($this->items, ...$arrays));
    }

    public function unique(): self
    {
        return self::create(array_unique($this->items));
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function filter(callable $callable = null): self
    {
        return self::create(array_filter($this->items, $callable));
    }

    public function filterKeys(callable $callable): self
    {
        return self::create(array_filter($this->items, $callable, ARRAY_FILTER_USE_KEY));
    }

    public function keys(): self
    {
        return self::create(array_keys($this->items));
    }

    public function values(): self
    {
        return self::create(array_values($this->items));
    }

    public function removeItem(mixed $item): self
    {
        $tmp = $this->items;

        $index = array_search($item, $this->items, true);
        unset($tmp[$index]);

        return self::create($tmp);
    }

    public function removeItems(array $itemsToRemove): self
    {
        $items = $this->items;

        foreach ($itemsToRemove as $itemToRemove) {
            $items = self::create($items)
                ->removeItem($itemToRemove)
                ->toArray();
        }

        return self::create($items);
    }

    public function add(mixed $item): self
    {
        return self::create([...$this->items, $item]);
    }

    public function removeAtBeginning(int $howMuch): self
    {
        $count = $this->count();

        if ($howMuch < 0 || $howMuch > $count) {
            throw new \LogicException('Cannot remove less than zero or more than item count in collection');
        }

        return self::create(array_slice($this->items, $howMuch, $count - $howMuch));
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function max(): int
    {
        return max($this->items);
    }

    public function min(): int
    {
        return min($this->items);
    }

    public function searchKey(callable $callable): int|string|null
    {
        $searchedIndex = null;

        foreach ($this->items as $index => $item) {
            if ($callable($item)) {
                $searchedIndex = $index;
                break;
            }
        }

        return $searchedIndex;
    }

    public function unsetKeys(array $indexesToRemove): self
    {
        $newItems = $this->items;

        foreach ($indexesToRemove as $index) {
            unset($newItems[$index]);
        }

        return self::create($newItems);
    }

    public function unset(callable $callable): self
    {
        $new = $this->items;

        foreach ($new as $index => $item) {
            if ($callable($item, $index)) {
                unset($new[$index]);
            }
        }

        return self::create($new);
    }

    public function insertItemAtIndex(mixed $item, int|string $newIndex): self
    {
        $new = $this->items;

        array_splice($new, $newIndex, 0, [$item]);

        return self::create($new);
    }

    public function filterKeysByValue(int|string ...$keys): self
    {
        return $this->filterKeys(static fn (int $index) => in_array($index, [...$keys], true));
    }

    public function get(int $index): mixed
    {
        return $this->items[$index] ?? null;
    }

    public function clone(): self
    {
        return self::create($this->items);
    }

    public function first(): mixed
    {
        $items = $this->items;

        $first = reset($items);

        return $first !== false ? $first : null;
    }

    public function moveFirstToEnd(): self
    {
        $items = $this->items;
        $firstItem = array_shift($items);

        $items[] = $firstItem;

        return self::create($items)->values();
    }

    public function contains(mixed $item): bool
    {
        return in_array($item, $this->items, true);
    }

    public function eachToString(): self
    {
        return $this->forEach(static fn (mixed $item): string => (string) $item);
    }

    public function hasItems(): bool
    {
        return $this->isEmpty() === false;
    }

    public function sort(): self
    {
        $items = $this->items;

        sort($items);

        return self::create($items);
    }
}
