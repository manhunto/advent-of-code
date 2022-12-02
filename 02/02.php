<?php

// https://adventofcode.com/2022/day/2

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
        if ($this === self::Rock && $other === self::Scissors) {
            return true;
        }

        if ($this === self::Paper && $other === self::Rock) {
            return true;
        }

        if ($this === self::Scissors && $other === self::Paper) {
            return true;
        }

        return false;
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
    $responseShape = Shape::decryptShape($response);

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
