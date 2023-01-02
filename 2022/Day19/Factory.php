<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day19;

class Factory implements \Stringable
{
    public array $robots;
    public array $inventory;
    private array $costs;
    private array $maxCosts;
    private string $id;

    public function __construct(array $costs, array $inventory = null, array $robots = null)
    {
        $this->id = uniqid();
        $this->robots = $robots ?: ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
        $this->inventory = $inventory ?: ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
        $this->costs = $costs;
        $this->maxCosts = ['ore' => 0, 'clay' => 0, 'obsidian' => 0];

        foreach ($this->costs as $robot => $cost) {
            if ($robot !== 'ore') {
                $this->maxCosts['ore'] = max($this->maxCosts['ore'], $cost['ore'] ?? 0);
            }

            if ($robot !== 'clay') {
                $this->maxCosts['clay'] = max($this->maxCosts['clay'], $cost['clay'] ?? 0);
            }

            if ($robot !== 'obsidian') {
                $this->maxCosts['obsidian'] = max($this->maxCosts['obsidian'], $cost['obsidian'] ?? 0);
            }
        }
    }

    public function clone(): \Generator
    {
        $hasEnoughOre = $this->maxCosts['ore'] <= $this->robots['ore'];
        $hasEnoughClay = $this->maxCosts['clay'] <= $this->robots['clay'];

        $canBuildGeode = $this->canBuildGeodeRobot();
        $canBuildObsidian = $this->canBuildObsidianRobot();
        $canBuildClay = $this->canBuildClayRobot();
        $canBuildOre = $this->canBuildOreRobot();

        $this->collect();

        $relevantRoboBuiltCount = 0;
        if ($canBuildGeode) {
            yield $this->withGeodeRobot();
        }
        if ($canBuildObsidian) {
            yield $this->withObsidianRobot();
        }
        if ($hasEnoughClay === false && $canBuildClay) {
            $relevantRoboBuiltCount++;
            yield $this->withClayRobot();
        }
        if ($hasEnoughOre === false && $canBuildOre) {
            $relevantRoboBuiltCount++;
            yield $this->withOreRobot();
        }

        if ($relevantRoboBuiltCount < 2){
            yield $this;
        }
    }

    private function withOreRobot(): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['ore']['ore'];

        $robots = $this->robots;
        ++$robots['ore'];

        return new self($this->costs, $inventory, $robots);
    }

    private function withClayRobot(): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['clay']['ore'];

        $robots = $this->robots;
        ++$robots['clay'];

        return new self($this->costs, $inventory, $robots);
    }

    private function withObsidianRobot(): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['obsidian']['ore'];
        $inventory['clay'] -= $this->costs['obsidian']['clay'];

        $robots = $this->robots;
        ++$robots['obsidian'];

        return new self($this->costs, $inventory, $robots);
    }

    private function withGeodeRobot(): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['geode']['ore'];
        $inventory['obsidian'] -= $this->costs['geode']['obsidian'];

        $robots = $this->robots;
        ++$robots['geode'];

        return new self($this->costs, $inventory, $robots);
    }

    private function collect(): void
    {
        $this->inventory['ore'] += $this->robots['ore'];
        $this->inventory['clay'] += $this->robots['clay'];
        $this->inventory['obsidian'] += $this->robots['obsidian'];
        $this->inventory['geode'] += $this->robots['geode'];
    }

    public function getGeode(): int
    {
        return $this->inventory['geode'];
    }

    public function __toString(): string
    {
        return sprintf('[r] %s|[i] %s', $this->getRobotsHash(), $this->getInventoryHash());
    }

    public function getRobotsHash(): string
    {
        $r = $this->robots;
        return sprintf('G:%d,O:%d,C:%d,OR:%d', $r['geode'], $r['obsidian'], $r['clay'], $r['ore']);
    }

    public function hasTheSameRobots(self $other): bool
    {
        return $this->getRobotsHash() === $other->getRobotsHash();
    }

    public function hasBetterInventory(self $other): bool
    {
        return $this->inventory['geode'] >= $other->inventory['geode']
            && $this->inventory['obsidian'] >= $other->inventory['obsidian']
            && $this->inventory['clay'] >= $other->inventory['clay']
            && $this->inventory['ore'] >= $other->inventory['ore'];
    }

    public function isTheSame(self $other): bool
    {
        return $this->id === $other->id;
    }

    private function canBuildOreRobot(): bool
    {
        $oreCosts = $this->costs['ore'];

        return $this->inventory['ore'] >= $oreCosts['ore'];
    }

    private function canBuildClayRobot(): bool
    {
        $clayCosts = $this->costs['clay'];

        return $this->inventory['ore'] >= $clayCosts['ore'];
    }

    private function canBuildObsidianRobot(): bool
    {
        $obsidianCosts = $this->costs['obsidian'];

        return $this->inventory['ore'] >= $obsidianCosts['ore']
            && $this->inventory['clay'] >= $obsidianCosts['clay'];
    }

    private function canBuildGeodeRobot(): bool
    {
        $geodeCosts = $this->costs['geode'];

        return $this->inventory['ore'] >= $geodeCosts['ore']
            && $this->inventory['obsidian'] >= $geodeCosts['obsidian'];
    }

    private function getInventoryHash(): string
    {
        $i = $this->inventory;
        return sprintf('G:%d,O:%d,C:%d,OR:%d', $i['geode'], $i['obsidian'], $i['clay'], $i['ore']);
    }
}
