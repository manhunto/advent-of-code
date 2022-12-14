<?php

declare(strict_types=1);

namespace App\Utils;

class Collection
{
    public function __construct(
        private readonly array $items
    ) {
    }

    public static function withExplode(string $separator, string $string): self
    {
        return self::create(explode($separator, $string));
    }

    public static function create(array $array): self
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
        return $this->getIndices(static fn (string $encodedPacket) => in_array($encodedPacket, $haystack, true));
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
