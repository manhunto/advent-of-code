<?php

declare(strict_types=1);

namespace App;

enum InputType: string
{
    case Puzzle = 'puzzle';
    case Example = 'example';
    case Example2 = 'example2';
    case Example3 = 'example3';
    case Example4 = 'example4';
    case Example5 = 'example5';

    public static function tryForExampleNumber(int $exampleNumber): self
    {
        $value = sprintf('example%s', $exampleNumber);
        $inputType = self::tryFrom($value);

        if ($inputType === null) {
            throw new \RuntimeException($value . ' is not available input type. You can use for: example2, example3.. up to 5.');
        }

        return $inputType;
    }
}
