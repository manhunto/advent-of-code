<?php

declare(strict_types=1);

namespace App\Utils;

class Regex
{
    public static function matchSingle(string $pattern, string $subject): mixed
    {
        preg_match($pattern, $subject, $matches);

        return $matches[1] ?? throw new \LogicException(sprintf('Cannot match pattern `%s` in string `%s', $pattern, $subject));
    }
}
