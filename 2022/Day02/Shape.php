<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day02;

enum Shape: int
{
    case Rock = 1;
    case Paper = 2;
    case Scissors = 3;

    public static function decryptShape(string $encryptedShape): self
    {
        return match ($encryptedShape) {
            'A', 'X' => self::Rock,
            'B', 'Y' => self::Paper,
            'C', 'Z' => self::Scissors,
        };
    }

    public function wins(Shape $other): bool
    {
        $toDefeat = $this->getShapeThatIDefeat();

        return $other === $toDefeat;
    }

    public function getShapeThatDefeatsMe(): Shape
    {
        $loosingValue = array_flip($this->winingMatrix())[$this->value];

        return self::from($loosingValue);
    }

    public function getShapeThatIDefeat(): Shape
    {
        $winningValue = $this->winingMatrix()[$this->value];

        return self::from($winningValue);
    }

    private function winingMatrix(): array
    {
        return [
            self::Rock->value => self::Scissors->value,
            self::Paper->value => self::Rock->value,
            self::Scissors->value => self::Paper->value
        ];
    }
}
