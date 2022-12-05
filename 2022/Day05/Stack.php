<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day05;

final class Stack
{
    public function __construct(
        private array $crates
    ) {
    }

    public function unshift(int $quantity): self
    {
        $oldCrates = $this->crates;
        $this->crates = array_slice($this->crates, $quantity, count($this->crates) - $quantity);

        $newCrates = array_reverse(array_slice($oldCrates, 0, $quantity));

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
}
