<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\FileNotFound;
use App\Input;
use App\InputType;
use App\Result;
use App\SolutionFile;

final class FileSystem
{
    /**
     * @throws FileNotFound
     */
    public function loadInput(Date $date, InputType $inputType): Input
    {
        $inputFileLocation = $this->buildFilePath($date, SolutionFile::in($inputType));
        $fileContent = $this->loadFile($inputFileLocation);

        return Input::fromArray($fileContent, $inputType);
    }

    /**
     * @throws FileNotFound
     */
    public function loadExpectedResult(Date $date, InputType $inputType): Result
    {
        $outputFileLocation = $this->buildFilePath($date, SolutionFile::out($inputType));
        $fileContent = $this->loadFile($outputFileLocation);

        return Result::fromArray($fileContent);
    }

    public function createSolutionDir(Date $date): void
    {
        $path = $this->buildDirPath($date);

        if (!is_dir($path) && !mkdir($path, recursive: true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }

    public function createFile(Date $date, string $fileName, ?string $content = null): void
    {
        $filePath = $this->buildFilePath($date, $fileName);

        if (!$this->fileExists($filePath)) {
            touch($filePath);

            if ($content) {
                file_put_contents($filePath, $content);
            }
        }
    }

    public function hasPuzzleInput(Date $date): bool
    {
        $filePath = $this->buildFilePath($date, SolutionFile::puzzleIn());

        return $this->fileExists($filePath);
    }

    public function hasPuzzleOutput(Date $date): bool
    {
        $filePath = $this->buildFilePath($date, SolutionFile::puzzleOut());

        return $this->fileExists($filePath);
    }

    public function savePuzzleAnswers(Date $date, Result $result): void
    {
        if ($result->hasAtLeasOneAnswer()) {
            $filePath = $this->buildFilePath($date, SolutionFile::puzzleOut());

            file_put_contents(
                $filePath,
                implode(PHP_EOL, $result->getAsArray()) . PHP_EOL,
            );
        }
    }

    private function fileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * @throws FileNotFound
     */
    private function loadFile(string $location): array
    {
        if (!$this->fileExists($location)) {
            throw new FileNotFound('Cannot load file. File location ' . $location);
        }

        return file($location, FILE_IGNORE_NEW_LINES);
    }

    private function buildFilePath(Date $date, string $fileName): string
    {
        return $this->buildDirPath($date) . '/' . $fileName;
    }

    private function buildDirPath(Date $date): string
    {
        return sprintf('%s/Day%s', $date->getYearAsString(), $date->day);
    }
}
