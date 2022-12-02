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

$input = file_get_contents('02_input.txt');
$lines = array_filter(explode(PHP_EOL, $input));
$totalSum = 0;

foreach ($lines as $singleGameGuide) {
    [$oponent, $response] = explode(' ', $singleGameGuide);

    $oponentShape = Shape::decryptShape($oponent);
    // >> first half
    $responseShape = Shape::decryptShape($response);
    // << first half

    // >> second half
    if ($response === LOOSE) {
        $responseShape = $oponentShape->getShapeThatIDefeat();
    } elseif($response === WIN) {
        $responseShape = $oponentShape->getShapeThatDefeatsMe();
    } else {
        $responseShape = $oponentShape;
    }
    // << second half

    if ($responseShape->wins($oponentShape)) {
        $totalSum += 6;
    } elseif($responseShape->equals($oponentShape)) {
        $totalSum += 3;
    }

    $pointsForShape = match ($responseShape) {
        Shape::Rock => 1,
        Shape::Paper => 2,
        Shape::Scissors => 3
    };

    $totalSum += $pointsForShape;
}

var_dump($totalSum);
