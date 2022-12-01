<?php

$input = file_get_contents('01_input.txt');

$food = explode(PHP_EOL, $input);

$elfs = [];
$elf = [];

foreach ($food as $foodItem) {
    if (empty($foodItem)) {
        $elfs[] = $elf;
        $elf = [];
    } else {
        $elf[] = (int) $foodItem;

    }
}

$elfsSum = array_map('array_sum', $elfs);
$max = max($elfsSum);

var_dump($max);
