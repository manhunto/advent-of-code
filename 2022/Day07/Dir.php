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
        $size = 0;

        foreach ($this->files as $file) {
            $size += $file->size;
        }

        foreach ($this->getSubDirs() as $dir) {
            $size += $dir->getTotalSize();
        }

        return $size;
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
        echo str_repeat(' ', $depth * 2) . '- ' . $this->name . ' (dir)' . PHP_EOL;

        foreach ($this->getSubDirs() as $subDir) {
            $subDir->print($depth + 1);
        }

        foreach ($this->files as $file) {
            echo str_repeat(' ', ($depth + 1) * 2) . '- ' . $file->name . ' (file, size=' . $file->size . ')' . PHP_EOL;
        }
    }

    private function addSubDir(Dir $subDir): void
    {
        $this->dirs[] = $subDir;
    }
}
