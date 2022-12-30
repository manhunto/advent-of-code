<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day19;

class Factory
{
    const ORE = 'ore';
    const CLAY = 'clay';
    const OBSIDIAN = 'obsidian';
    const GEODE = 'geode';

    private array $robots;
    private array $inventory;
    private array $costs;
    private array $maxCosts;

    public function __construct(array $costs, array $inventory = null, array $robots = null)
    {
        $this->robots = $robots ?: ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
        $this->inventory = $inventory ?: ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];
        $this->maxCosts = [
            'ore' => PHP_INT_MIN,
            'clay' => PHP_INT_MIN,
            'obsidian' => PHP_INT_MIN,
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
    }

    public function clone(): \Generator
    {
        $this->collect();

        $geodeCosts = $this->costs['geode'];
        $canBuildGeode = $this->inventory['ore'] >= $geodeCosts['ore'] && $this->inventory['obsidian'] >= $geodeCosts['obsidian'];

        $obsidianCosts = $this->costs['obsidian'];
        $canBuildObsidian = $this->inventory['ore'] >= $obsidianCosts['ore'] && $this->inventory['clay'] >= $obsidianCosts['clay'];

        $clayCosts = $this->costs['clay'];
        $canBuildClay = $this->inventory['ore'] >= $clayCosts['ore'];

        $oreCosts = $this->costs['ore'];
        $canBuildOre = $this->inventory['ore'] >= $oreCosts['ore'];

//        $hasEnoughObsidian = false;
//        $hasEnoughClay = false;
//        $hasEnoughOre = false;
        $hasEnoughObsidian = $this->maxCosts['obsidian'] < $this->robots['obsidian'] || $this->maxCosts['obsidian'] < $this->inventory['obsidian'];
        $hasEnoughClay = $this->maxCosts['clay'] < $this->robots['clay'] || $this->maxCosts['clay'] < $this->inventory['clay'];
        $hasEnoughOre = $this->maxCosts['ore'] < $this->robots['ore'] || $this->maxCosts['ore'] < $this->inventory['ore'];


        if ($canBuildGeode) {
            yield $this->withGeodeRobot();
        } if ($hasEnoughObsidian === false && $canBuildObsidian) {
            yield $this->withObsidianRobot();
        } if ($hasEnoughClay === false && $canBuildClay) {
            yield $this->withClayRobot();
        } if ($hasEnoughOre === false && $canBuildOre) {
            yield $this->withOreRobot();
        }

        if ($hasEnoughClay) {
            return;
        }

        if ($hasEnoughOre) {
            return;
        }

        if ($hasEnoughObsidian) {
            return;
        }

        yield $this;
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
        return $this->inventory['ore'];
    }
}
