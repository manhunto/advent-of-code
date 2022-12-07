<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day07;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'No Space Left On Device',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        /** @var ?Dir $currentDir */
        $currentDir = null;
        /** @var ?Dir $root */
        $root = null;

        foreach ($input->asArray() as $row) {
            if (preg_match('/\$ cd (.*)/', $row, $matches)) {
                $dirName = $matches[1];

                if ($dirName === '/') {
                    $currentDir = $root = new Dir($dirName);
                } elseif (preg_match('/[a-z]+/', $dirName)) {
                    $currentDir = new Dir($dirName, $currentDir);
                } elseif ($dirName === '..') {
                    $currentDir = $currentDir->getParent();
                }
            } else if (preg_match('/(\d+) ([a-z\.]+)/', $row, $matches)) {
                $currentDir->addFile($matches[2], (int) $matches[1]);
            }
        }

        $sizes = $this->getSizes($root);

        $root->print();

        $partOne = $this->getSumOfDirsTotalSizeOfAtMost100000($sizes);
        $partTwo = $this->getSmallestDirThatWouldFreeUpEnoughSpaceToRunUpdate($sizes, $root);

        return new Result($partOne, $partTwo);
    }

    private function getSizes(Dir $dir, array &$sizes = []): array
    {
        $sizes[$dir->getFullName()] = $dir->getTotalSize();

        foreach ($dir->getSubDirs() as $subDir) {
            $this->getSizes($subDir, $sizes);
        }

        return $sizes;
    }

    private function getSumOfDirsTotalSizeOfAtMost100000(array $sizes): int
    {
        $atMost = array_filter($sizes, static fn (int $size) => $size <= 100000);

        return array_sum($atMost);
    }

    private function getSmallestDirThatWouldFreeUpEnoughSpaceToRunUpdate(array $sizes, Dir $root)
    {
        $totalDiscAvailable = 70_000_000;
        $unusedSpaceToRunUpdate = 30_000_000;

        sort($sizes);

        $unusedSpace = $totalDiscAvailable - $root->getTotalSize();
        $toDelete = $unusedSpaceToRunUpdate - $unusedSpace;

        foreach ($sizes as $size) {
            if ($toDelete <= $size) {
                return $size;
            }
        }
    }
}
