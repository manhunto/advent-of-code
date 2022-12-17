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

    public function add(mixed $item): self
    {
        return self::create([...$this->items, $item]);
    }

    public function removeAtBeginning(int $howMuch): self
    {
        $count = count($this->items);

        if ($howMuch <= 0 || $howMuch >= $count) {
            throw new \LogicException('Cannot remove zero or all items from collection');
        }

        return self::create(array_slice($this->items, -1 * ($count - $howMuch)));
    }
}
