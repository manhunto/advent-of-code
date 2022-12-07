<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day07;

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: 'TODO Change me',
)]
final class Solution implements Solver
{
    public function solve(Input $input): Result
    {
        /** @var Dir|null $currentDir */
        $currentDir = null;
        $root = null;

        foreach ($input->asArray() as $row) {
            if (preg_match('/\$ cd (.*)/', $row, $matches)) {
                $dirName = $matches[1];

                if ($dirName === '/') {
                    $currentDir = $root = new Dir($dirName);
                    continue;
                }

                if (preg_match('/[a-z]+/', $dirName)) {
                    $currentDir = new Dir($dirName, $currentDir);
                    continue;
                }

                if ($dirName === '..') {
                    $currentDir = $currentDir->getParent();
                }
            } else if (preg_match('/(\d+) ([a-z\.]+)/', $row, $matches)) {
                $file = new File($matches[2], (int) $matches[1]);
                $currentDir->addFile($file);
            }
        }

        $root->print();

        $sizes = $this->getSizes($root);
        var_dump($sizes);

        $atMost = array_filter($sizes, fn (int $size) => $size <= 100000);

        return new Result(array_sum($atMost));
    }

    private function getSizes(Dir $dir, array &$sizes = []): array
    {
        $sizes[$dir->getFullName()] = $dir->getTotalSize();

        foreach ($dir->getSubDirs() as $subDir) {
            $this->getSizes($subDir, $sizes);
        }

        return $sizes;
    }
}
