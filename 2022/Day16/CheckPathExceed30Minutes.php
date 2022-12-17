<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day16;

use App\Utils\PathFinding\Node;
use App\Utils\PathFinding\Utils\CanVisitNodeInterface;

class CheckPathExceed30Minutes implements CanVisitNodeInterface
{
    private ValveHelpers $helper;

    /** @param Valve[] $valves */
    public function __construct(
        private readonly array $valves,
        private readonly array $pathsFromValveToValve,
    ) {
        $this->helper = new ValveHelpers();
    }

    public function canMove(array $currentPath, Node $node): bool
    {
        $path = [...$currentPath, $node->name];

        $fullPath = $this->helper->convertPathBetweenOpenableValvesToFullPath(
            $path,
            $this->valves,
            $this->pathsFromValveToValve
        );

        return count($fullPath) <= 30;
    }
}
