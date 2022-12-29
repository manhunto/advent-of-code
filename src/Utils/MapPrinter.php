<?php

declare(strict_types=1);

namespace App\Utils;

class MapPrinter
{
    private bool $reversedHorizontally = true;
    private bool $withRowNumbers = true;

    public function __construct(
        private array $map,
    ) {
    }

    public function naturalHorizontally(): self
    {
        $this->reversedHorizontally = false;

        return $this;
    }

    public function withoutRowNumbers(): self
    {
        $this->withRowNumbers = false;

        return $this;
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
            $valueToPrint = implode('', $item);
            if ($this->withRowNumbers) {
                echo sprintf('%s |%s|',
                        str_pad((string)$row, $digits + 1, ' ', STR_PAD_LEFT),
                        $valueToPrint
                    ) . PHP_EOL;
            } else {
                echo $valueToPrint . PHP_EOL;
            }
        }
    }

    public function drawTemporaryPoint(Location $point, string $element): self
    {
        $this->map[$point->y][$point->x] = $element;

        return $this;
    }
}
