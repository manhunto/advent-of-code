<?php

// https://adventofcode.com/2022/day/1

$input = file_get_contents('01_input.txt');

$food = explode(PHP_EOL, $input);

$calories = [];
$caloriesForOneElf = 0;

foreach ($food as $foodItem) {
    if (empty($foodItem)) {
        $calories[] = $caloriesForOneElf;
        $caloriesForOneElf = 0;

        continue;
    }

    $caloriesForOneElf += (int) $foodItem;
}

// first part
$max = max($calories);

var_dump($max);

// second part
rsort($calories);

$topThreeElves = array_slice($calories, 0, 3);
$sumOfThree = array_sum($topThreeElves);

var_dump($sumOfThree);
