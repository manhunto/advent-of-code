<?php

// https://adventofcode.com/2022/day/2

const LOOSE = 'X';
const WIN = 'Z';

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

function calculatePoints(Shape $responseShape, Shape $oponentShape): int
{
    $points = $responseShape->value;

    if ($responseShape->wins($oponentShape)) {
        $points += 6;
    } elseif($responseShape === $oponentShape) {
        $points += 3;
    }

    return $points;
}

$lines = file('02_input.txt', FILE_IGNORE_NEW_LINES);

$totalSum1 = 0;
$totalSum2 = 0;

foreach ($lines as $singleGameGuide) {
    [$oponent, $response] = explode(' ', $singleGameGuide);

    $oponentShape = Shape::decryptShape($oponent);
    $responseShape = Shape::decryptShape($response);

    if ($response === LOOSE) {
        $responseShape2 = $oponentShape->getShapeThatIDefeat();
    } elseif($response === WIN) {
        $responseShape2 = $oponentShape->getShapeThatDefeatsMe();
    } else {
        $responseShape2 = $oponentShape;
    }

    $totalSum1 += calculatePoints($responseShape, $oponentShape);
    $totalSum2 += calculatePoints($responseShape2, $oponentShape);
}

echo 'First part: ' . $totalSum1 . PHP_EOL;
echo 'Second part: ' . $totalSum2 . PHP_EOL;
