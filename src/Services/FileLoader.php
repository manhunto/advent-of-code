<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\FileNotFound;
use App\InputType;

final class FileLoader
{
    private const EXTENSION_IN = 'in';
    private const EXTENSION_OUT = 'out';

    /**
     * @throws FileNotFound
     */
    public function loadInput(Date $date, InputType $inputType): array
    {
        $inputFileLocation = $this->buildFilePath($date, $inputType, self::EXTENSION_IN);

        return $this->loadFile($inputFileLocation);
    }

    /**
     * @throws FileNotFound
     */
    public function loadExpectedOutput(Date $date, InputType $inputType): array
    {
        $outputFileLocation = $this->buildFilePath($date, $inputType, self::EXTENSION_OUT);

        return $this->loadFile($outputFileLocation);
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
