<?php

declare(strict_types=1);

namespace App\Utils;

class Collection
{
    public static function withOneAsFirstIndex(array $array): array
    {
        return array_combine(
            keys: range(1, count($array)),
            values: array_values($array)
        );
    }
}
