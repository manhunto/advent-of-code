<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\FileNotFound;
use App\Input;
use App\InputType;
use App\Result;

final class FileLoader
{
    private const EXTENSION_IN = 'in';
    private const EXTENSION_OUT = 'out';

    /**
     * @throws FileNotFound
     */
    public function loadInput(Date $date, InputType $inputType): Input
    {
        $inputFileLocation = $this->buildFilePath($date, $inputType, self::EXTENSION_IN);
        $fileContent = $this->loadFile($inputFileLocation);

        return Input::fromArray($fileContent);
    }

    /**
     * @throws FileNotFound
     */
    public function loadExpectedResult(Date $date, InputType $inputType): Result
    {
        $outputFileLocation = $this->buildFilePath($date, $inputType, self::EXTENSION_OUT);
        $fileContent = $this->loadFile($outputFileLocation);

        return Result::fromArray($fileContent);
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

    private function buildFilePath(Date $date, InputType $inputType, string $extension): string
    {
        return sprintf('%s/Day%s/%s.%s', $date->getYearAsString(), $date->day, $inputType->value, $extension);
    }
}
