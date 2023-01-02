<?php

declare(strict_types=1);

namespace App;

class SolutionFile
{
    private const EXTENSION_IN = 'in';
    private const EXTENSION_OUT = 'out';

    public static function puzzleIn(): string
    {
        return self::in(InputType::Puzzle);
    }

    public static function exampleIn(): string
    {
        return self::in(InputType::Example);
    }

    public static function in(InputType $inputType): string
    {
        return self::buildFileName($inputType, self::EXTENSION_IN);
    }

    public static function puzzleOut(): string
    {
        return self::buildFileName(InputType::Puzzle, self::EXTENSION_OUT);
    }

    public static function exampleOut(): string
    {
        return self::buildFileName(InputType::Example, self::EXTENSION_OUT);
    }

    public static function out(InputType $inputType): string
    {
        return self::buildFileName($inputType, self::EXTENSION_OUT);
    }

    private static function buildFileName(InputType $inputType, string $extension): string
    {
        return sprintf('%s.%s', $inputType->value, $extension);
    }
}
