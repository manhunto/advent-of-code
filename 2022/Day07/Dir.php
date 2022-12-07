<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day07;

final class Dir
{
    /** @var File[] */
    private array $files = [];

    /** @var Dir[] */
    private array $dirs = [];

    public function __construct(
        private readonly string $name,
        private readonly ?Dir $parent = null,
    ) {
        if ($parent) {
            $this->parent->addSubDir($this);
        }
    }

    public function getParent(): ?Dir
    {
        return $this->parent;
    }

    public function addFile(string $name, int $size): void
    {
        $this->files[] = new File($name, $size);
    }

    /**
     * @return Dir[]
     */
    public function getSubDirs(): array
    {
        return $this->dirs;
    }

    public function getTotalSize(): int
    {
        $dirSize = array_reduce($this->files, static fn (int $sum, File $file) => $sum + $file->size, 0);
        $fileSize = array_reduce($this->getSubDirs(), static fn (int $sum, Dir $dir) => $sum + $dir->getTotalSize(), 0);

        return $dirSize + $fileSize;
    }

    public function getFullName(): string
    {
        if ($this->parent === null) {
            return '';
        }

        return $this->parent->getFullName() . '/' . $this->name;
    }

    public function print(int $depth = 0): void
    {
        $this->printRow($depth, $this->name, '(dir)');

        foreach ($this->getSubDirs() as $subDir) {
            $subDir->print($depth + 1);
        }

        foreach ($this->files as $file) {
            $this->printRow($depth + 1, $file->name, sprintf('(file, size=%s)', $file->size));
        }
    }

    private function addSubDir(Dir $subDir): void
    {
        $this->dirs[] = $subDir;
    }

    private function printRow(int $depth, string $name, string $description): void
    {
        echo str_repeat(' ', $depth * 2) . '- ' . $name . ' ' . $description . PHP_EOL;
    }
}
