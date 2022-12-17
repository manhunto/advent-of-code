<?php

declare(strict_types=1);

namespace App\Utils\PathFinding;

class EveryPossiblePathGenerator
{
    private array $paths = [];
    private readonly Node $startNode;
    /** @var array<string, Node> */
    private array $nodes;

    /**
     * @param Node[] $nodes
     */
    public function __construct(
        array $nodes,
        string $startAt,
        private readonly int $maxMoves,
    ) {
        foreach ($nodes as $node) {
            $this->nodes[$node->name] = $node;
        }

        $this->startNode = $this->getNode($startAt);
    }

    public function generate(): array
    {
        $this->visit($this->startNode);

        return $this->paths;
    }

    private function visit(Node $nodeToVisit, array $currentPath = [], int $move = 0): void
    {
        $currentPath[] = $nodeToVisit->name;

        if ($move >= $this->maxMoves) {
            $this->paths[] = $currentPath;

            return;
        }

        foreach ($nodeToVisit->neighbours as $neighbour) {
            $node = $this->getNode($neighbour);

            if ($node->visitableOnlyOnce === false || ($node->visitableOnlyOnce && in_array($node->name, $currentPath, true) === false)) {
                $this->visit($node, $currentPath, $move + 1);
            }
        }
    }

    private function getNode(string $nodeName)
    {
        return $this->nodes[$nodeName] ?? throw new \LogicException('Cannot find node with name ' . $nodeName);
    }
}
