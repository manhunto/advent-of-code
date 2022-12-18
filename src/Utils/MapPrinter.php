<?php

declare(strict_types=1);

namespace App\Utils;

class MapPrinter
{
    private bool $reversedHorizontally = true;

    public function __construct(
        private array $map,
    )
    {
    }

    public function drawTemporaryShape(array $shape, string $string): self
    {
        foreach ($shape as $y => $row) {
            foreach ($row as $x => $value) {
                $this->map[$y][$x] = $string;
            }
        }

        return $this;
    }


    public function print(): void
    {
        $map = $this->reversedHorizontally ? array_reverse($this->map, true) : $this->map;
        $maxY = Collection::create($map)
            ->keys()
            ->max();

        $digits = strlen((string) $maxY);

        foreach ($map as $row => $item) {
            echo sprintf('%s |%s|',
                    str_pad((string)$row, $digits + 1, ' ', STR_PAD_LEFT),
                    implode('', $item)
                ) . PHP_EOL;
        }
    }
}
