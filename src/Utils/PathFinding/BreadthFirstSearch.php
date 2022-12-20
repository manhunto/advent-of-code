<?php

declare(strict_types=1);

namespace App\Utils\PathFinding;

use SplQueue;

/**
 * Finding the shortest path for unweighted graph, one direction
 * FIFO
 */
class BreadthFirstSearch
{
    /**
     * @param array<string, string[]> $graph [node][] = neighbour
     * @param string $start
     * @param array $endNodes
     * @return array
     */
    public function getPath(array $graph, string $start, array $endNodes = []): array
    {
        if (in_array($start, $endNodes, true)) {
            return [$start];
        }

        $queue = new SplQueue();
        $queue->enqueue([$start]);
        $visited = [$start];

        while (!$queue->isEmpty()) {
            $path = $queue->dequeue();
            $node = end($path);

            if (in_array($node, $endNodes, true)) {
                return $path;
            }

            foreach ($graph[$node] as $neighbour) {
                if (!in_array($neighbour, $visited, true)) {
                    $visited[] = $neighbour;

                    $newPath = $path;
                    $newPath[] = $neighbour;

                    $queue->enqueue($newPath);
                }
            }
        }

        throw new \LogicException('Cannot reach any of end nodes');
    }
}
