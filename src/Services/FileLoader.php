<?php

declare(strict_types=1);

namespace App\Services;

use App\Date;
use App\Exceptions\FileNotFound;
use App\Input;
use App\InputType;

final class FileLoader
{
    /**
     * @throws FileNotFound
     */
    public function loadInput(Date $date, InputType $inputType): Input
    {
        $inputFileLocation = sprintf('%s/Day%s/%s.in', $date->year, $date->day, $inputType->value);

        return $this->loadFile($inputFileLocation);
    }

    /**
     * @throws FileNotFound
     */
    public function loadExpectedOutput(Date $date, InputType $inputType): Input
    {
        $outputFileLocation = sprintf('%s/Day%s/%s.out', $date->year, $date->day, $inputType->value);

        return $this->loadFile($outputFileLocation);
    }

    /**
     * @throws FileNotFound
     */
    private function loadFile(string $location): Input
    {
        if (!file_exists($location)) {
            throw new FileNotFound('Cannot load file. File location ' . $location);
        }

        $content = file($location, FILE_IGNORE_NEW_LINES);

        return Input::fromArray($content);
    }
}
