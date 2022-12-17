<?php

declare(strict_types=1);

namespace App\Utils\PathFinding;

use App\Utils\PathFinding\Utils\CanVisitNodeInterface;
use App\Utils\PathFinding\Utils\DummyCanVisitNode;

/**
 * Generates paths which every node should be visited once or zero times
 */
class EveryPossiblePathGenerator
{
    private array $paths = [];
    private readonly Node $startNode;
    /** @var array<string, Node> */
    private array $nodes;
    private CanVisitNodeInterface $canVisit;

    /**
     * @param Node[] $nodes
     */
    public function __construct(
        array $nodes,
        string $startAt,
        CanVisitNodeInterface $canVisitNode = null
    ) {
        foreach ($nodes as $node) {
            $this->nodes[$node->name] = $node;
        }

        $this->startNode = $this->getNode($startAt);
        $this->canVisit = $canVisitNode ?: new DummyCanVisitNode();
    }

    public function generate(): array
    {
        $this->visit($this->startNode);

        return $this->paths;
    }

    private function visit(Node $nodeToVisit, array $currentPath = []): void
    {
        $currentPath[] = $nodeToVisit->name;
        $this->paths[] = $currentPath;

        foreach ($nodeToVisit->neighbours as $neighbour) {
            $node = $this->getNode($neighbour);

            if (in_array($node->name, $currentPath, true)) {
                continue;
            }

            if ($this->canVisit->canMove($currentPath, $node) === false) {
                continue;
            }

            $this->visit($node, $currentPath);
        }
    }

    private function getNode(string $nodeName)
    {
        return $this->nodes[$nodeName] ?? throw new \LogicException('Cannot find node with name ' . $nodeName);
    }
}
