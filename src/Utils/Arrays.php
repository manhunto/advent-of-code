<?php

declare(strict_types=1);

namespace App\Utils;

class Arrays
{
    public static function sortedAsc(array $array): array
    {
        sort($array);

        return $array;
    }
}
