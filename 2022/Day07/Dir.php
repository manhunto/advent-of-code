<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day07;

class Dir
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

    public function addFile(File $file): void
    {
        $this->files[] = $file;
    }

    private function addSubDir(Dir $subDir): void
    {
        $this->dirs[] = $subDir;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasSubDirs(): bool
    {
        return !empty($this->dirs);
    }

    /**
     * @return Dir[]
     */
    public function getSubDirs(): array
    {
        return $this->dirs;
    }

    public function hasFiles(): bool
    {
        return !empty($this->files);
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function getTotalSize(): int
    {
        $size = 0;

        foreach ($this->files as $file) {
            $size += $file->getSize();
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

        $name = '';

        if ($this->parent) {
            $name .= $this->parent->getFullName() . '/';
        }

        $name .= $this->name;

        return $name;
    }

    public function print(int $depth = 0): void
    {
        echo str_repeat(' ', $depth * 2) . '- ' . $this->getName() . ' (dir)' . PHP_EOL;

        foreach ($this->getSubDirs() as $subDir) {
            $subDir->print($depth + 1);
        }

        foreach ($this->getFiles() as $file) {
            echo str_repeat(' ', ($depth + 1) * 2) . '- ' . $file->getName() . ' (file, size=' . $file->getSize() . ')' . PHP_EOL;
        }
    }
}
