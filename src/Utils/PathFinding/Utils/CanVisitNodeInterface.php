<?php

declare(strict_types=1);

namespace App\Utils\PathFinding\Utils;

use App\Utils\PathFinding\Node;

interface CanVisitNodeInterface
{
    /**
     * @param string[] $currentPath
     */
    public function canMove(array $currentPath, Node $node): bool;
}
