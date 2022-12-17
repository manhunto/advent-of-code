<?php

declare(strict_types=1);

namespace App\Utils\PathFinding\Utils;

use App\Utils\PathFinding\Node;

class DummyCanVisitNode implements CanVisitNodeInterface
{
    public function canMove(array $currentPath, Node $node): bool
    {
        return true;
    }
}
