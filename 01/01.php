<?php

// https://adventofcode.com/2022/day/1

$input = file('01_input.txt', FILE_IGNORE_NEW_LINES);

$calories = [];
$caloriesForOneElf = 0;

foreach ($input as $foodItem) {
    if (empty($foodItem)) {
        $calories[] = $caloriesForOneElf;
        $caloriesForOneElf = 0;

        continue;
    }

    $caloriesForOneElf += (int) $foodItem;
}

// first part
$max = max($calories);

echo 'First part: ' . $max . PHP_EOL;

// second part
rsort($calories);
$topThreeElves = array_slice($calories, 0, 3);
$sumOfThree = array_sum($topThreeElves);

echo 'Second part: ' . $sumOfThree . PHP_EOL;
