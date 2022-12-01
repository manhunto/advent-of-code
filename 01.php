<?php

$input = file_get_contents('01_input.txt');

$food = explode(PHP_EOL, $input);

$elves = [];
$elf = [];

foreach ($food as $foodItem) {
    if (empty($foodItem)) {
        $elves[] = $elf;
        $elf = [];
    } else {
        $elf[] = (int) $foodItem;
    }
}

$elvesSum = array_map('array_sum', $elves);

// first part
$max = max($elvesSum);

var_dump($max);

// secondPart
rsort($elvesSum);

$topThreeElves = array_slice($elvesSum, 0, 3);
$sumOfThree = array_sum($topThreeElves);
var_dump($sumOfThree);
