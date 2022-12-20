<?php

declare(strict_types=1);

namespace App\Utils;

use SplQueue;

/**
 * Visit every possible node in graph using BFS algorithm
 * It returns visited nodes
 */
class FloodFill
{
    /**
     * @param array<string, string[]> $graph
     * @param mixed $start
     * @return string[]
     */
    public function fill(array $graph, string $start): array
    {
        $queue = new SplQueue();
        $queue->enqueue([$start]);
        $visited = [$start];

        while (!$queue->isEmpty()) {
            $path = $queue->dequeue();
            $node = end($path);

            foreach ($graph[$node] as $neighbour) {
                if (!in_array($neighbour, $visited, true)) {
                    $visited[] = $neighbour;

                    $newPath = $path;
                    $newPath[] = $neighbour;

                    $queue->enqueue($newPath);
                }
            }
        }

        return $visited;
    }
}
