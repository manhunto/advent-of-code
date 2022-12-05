<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day05;

final class Stack
{
    public function __construct(
        private array $crates
    ) {
    }

    public function unshiftOneByOne(int $quantity): self
    {
        $newCrates = array_reverse($this->unshiftAltogether($quantity)->crates);

        return new self($newCrates);
    }

    public function add(Stack $stack): void
    {
        $this->crates = array_merge($stack->crates, $this->crates);
    }

    public function getTopCrate()
    {
        return reset($this->crates);
    }

    public function unshiftAltogether(int $quantity): Stack
    {
        $oldCrates = $this->crates;
        $this->crates = array_slice($this->crates, $quantity, count($this->crates) - $quantity);

        return new self(array_slice($oldCrates, 0, $quantity));
    }
}
