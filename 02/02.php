<?php

// https://adventofcode.com/2022/day/2

const LOOSE = 'X';
const WIN = 'Z';

enum Shape
{
    case Rock;
    case Paper;
    case Scissors;

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
        return match ($this) {
            self::Rock => self::Paper,
            self::Paper => self::Scissors,
            self::Scissors => self::Rock
        };
    }

    public function getShapeThatIDefeat(): Shape
    {
        return match ($this) {
            self::Rock => self::Scissors,
            self::Paper => self::Rock,
            self::Scissors => self::Paper
        };
    }

    public function equals(Shape $other): bool
    {
        return $this === $other;
    }
}

function calculatePoints(Shape $responseShape, Shape $oponentShape) : int
{
    $points = 0;

    if ($responseShape->wins($oponentShape)) {
        $points += 6;
    } elseif($responseShape->equals($oponentShape)) {
        $points += 3;
    }

    $points += match ($responseShape) {
        Shape::Rock => 1,
        Shape::Paper => 2,
        Shape::Scissors => 3
    };

    return $points;
}

$input = file_get_contents('02_input.txt');
$lines = array_filter(explode(PHP_EOL, $input));

// First part
$totalSum = 0;
foreach ($lines as $singleGameGuide) {
    [$oponent, $response] = explode(' ', $singleGameGuide);

    $oponentShape = Shape::decryptShape($oponent);
    $responseShape = Shape::decryptShape($response);

    $totalSum += calculatePoints($responseShape, $oponentShape);
}

echo 'First part: ' . $totalSum . PHP_EOL;

// Second part
$totalSum = 0;
foreach ($lines as $singleGameGuide) {
    [$oponent, $response] = explode(' ', $singleGameGuide);

    $oponentShape = Shape::decryptShape($oponent);

    if ($response === LOOSE) {
        $responseShape = $oponentShape->getShapeThatIDefeat();
    } elseif($response === WIN) {
        $responseShape = $oponentShape->getShapeThatDefeatsMe();
    } else {
        $responseShape = $oponentShape;
    }

    $totalSum += calculatePoints($responseShape, $oponentShape);
}

echo 'Second part: ' . $totalSum . PHP_EOL;
