<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\FileNotFound;
use App\Input;
use App\InputType;
use App\Result;

final class FileSystem
{
    public const EXTENSION_IN = 'in';
    public const EXTENSION_OUT = 'out';

    /**
     * @throws FileNotFound
     */
    public function loadInput(Date $date, InputType $inputType): Input
    {
        $inputFileLocation = $this->buildFilePath($date, $this->buildFileName($inputType, self::EXTENSION_IN));
        $fileContent = $this->loadFile($inputFileLocation);

        return Input::fromArray($fileContent);
    }

    /**
     * @throws FileNotFound
     */
    public function loadExpectedResult(Date $date, InputType $inputType): Result
    {
        $outputFileLocation = $this->buildFilePath($date, $this->buildFileName($inputType, self::EXTENSION_OUT));
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
        $filePath= $this->buildFilePath($date, $fileName);

        if (!file_exists($filePath)) {
            touch($filePath);

            if ($content) {
                file_put_contents($filePath, $content);
            }
        }
    }

    public function savePuzzleAnswers(Date $date, Result $result): void
    {
        if ($result->hasAtLeasOneAnswer()) {
            $fileName = $this->buildFileName(InputType::Puzzle, self::EXTENSION_OUT);
            $filePath = $this->buildFilePath($date, $fileName);

            file_put_contents(
                $filePath,
                implode(PHP_EOL, $result->getAsArray()) . PHP_EOL,
            );
        }
    }

    /**
     * @throws FileNotFound
     */
    private function loadFile(string $location): array
    {
        if (!file_exists($location)) {
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

    private function buildFileName(InputType $inputType, string $extension): string
    {
        return sprintf('/%s.%s', $inputType->value, $extension);
    }
}
