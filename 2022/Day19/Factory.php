<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day19;

class Factory implements \Stringable
{
    const ORE = 'ore';
    const CLAY = 'clay';
    const OBSIDIAN = 'obsidian';
    const GEODE = 'geode';

    public array $robots;
    public array $inventory;
    private array $costs;
    private array $maxCosts;
    private array $history = [];
    private string $id;

    public function __construct(array $costs, array $inventory = null, array $robots = null, array $history = null)
    {
        $this->id = uniqid();
        $this->robots = $robots ?: ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
        $this->inventory = $inventory ?: ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
        $this->maxCosts = [
            'ore' => 0,
            'clay' => 0,
            'obsidian' => 0,
        ];

        $this->costs = $costs;

        foreach ($this->costs as $cost) {
            if (isset($cost['ore'])) {
                $this->maxCosts['ore'] = max($this->maxCosts['ore'], $cost['ore']);
            }

            if (isset($cost['clay'])) {
                $this->maxCosts['clay'] = max($this->maxCosts['clay'], $cost['clay']);
            }

            if (isset($cost['obsidian'])) {
                $this->maxCosts['obsidian'] = max($this->maxCosts['obsidian'], $cost['obsidian']);
            }
        }
        $this->history = $history ?: [];
    }

    public function clone(int $minute): \Generator
    {
        $hasEnoughOre = $this->maxCosts['ore'] <= $this->robots['ore'];
        $hasEnoughClay = $this->maxCosts['clay'] <= $this->robots['clay'];

        $geodeCosts = $this->costs['geode'];
        $canBuildGeode = $this->inventory['ore'] >= $geodeCosts['ore'] && $this->inventory['obsidian'] >= $geodeCosts['obsidian'];

        $obsidianCosts = $this->costs['obsidian'];
        $canBuildObsidian = $this->inventory['ore'] >= $obsidianCosts['ore'] && $this->inventory['clay'] >= $obsidianCosts['clay'];

        $clayCosts = $this->costs['clay'];
        $canBuildClay = $this->inventory['ore'] >= $clayCosts['ore'];

        $oreCosts = $this->costs['ore'];
        $canBuildOre = $this->inventory['ore'] >= $oreCosts['ore'];

        $this->collect();
        $this->addHistory($minute, (string) $this);

        if ($canBuildGeode) {
            yield $this->withGeodeRobot($minute);
        } if ($canBuildObsidian) {
            yield $this->withObsidianRobot($minute);
        } if ($hasEnoughClay === false && $canBuildClay) {
            yield $this->withClayRobot($minute);
        } if ($hasEnoughOre === false && $canBuildOre) {
            yield $this->withOreRobot($minute);
        }

//        if ($hasEnoughOre || $hasEnoughClay) {
//            return;
//        }

        yield $this;
    }

    private function withOreRobot(int $minute): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['ore']['ore'];

        $robots = $this->robots;
        ++$robots['ore'];

//        $history = $this->history;
//        $history[$minute][] = 'Ore';

        return new self($this->costs, $inventory, $robots);
    }

    private function withClayRobot(int $minute): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['clay']['ore'];

        $robots = $this->robots;
        ++$robots['clay'];

//        $history = $this->history;
//        $history[$minute][] = 'Clay';

        return new self($this->costs, $inventory, $robots);
    }

    private function withObsidianRobot(int $minute): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['obsidian']['ore'];
        $inventory['clay'] -= $this->costs['obsidian']['clay'];

        $robots = $this->robots;
        ++$robots['obsidian'];

//        $history = $this->history;
//        $history[$minute][] = 'Obsidian';

        return new self($this->costs, $inventory, $robots);
    }

    private function withGeodeRobot(int $minute): self
    {
        $inventory = $this->inventory;
        $inventory['ore'] -= $this->costs['geode']['ore'];
        $inventory['obsidian'] -= $this->costs['geode']['obsidian'];

        $robots = $this->robots;
        ++$robots['geode'];

//        $history = $this->history;
//        $history[$minute][] = 'Geode';

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
        return json_encode([
            'r' => $this->robots,
            'i' => $this->inventory,
        ]);
    }

    private function addHistory(int $minute, string $param)
    {
        $this->history[$minute][] = $param;
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
}
