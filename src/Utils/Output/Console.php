<?php

declare(strict_types=1);

namespace App\Utils\Output;

use JetBrains\PhpStorm\NoReturn;

class Console
{
    #[NoReturn]
    public static function dd(...$thing): void
    {
        var_dump(...$thing); die;
    }

    public static function wait(): void
    {
        readline();
    }

    public static function writeln($msg =  null): void
    {
        echo $msg . PHP_EOL;
    }

    public static function arrayToString(array $array): void
    {
        foreach ($array as $item) {
            self::writeln($item);
        }
    }

    public static function print_r(array $array): void
    {
        print_r($array);
    }
}
