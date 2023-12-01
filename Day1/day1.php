<?php

const NUMBERS = ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9];

function result(array $input, callable $filterFunction, array $numbers = NUMBERS): int
{
    return array_reduce(
        array_map(
            fn($line) => $filterFunction($line, $numbers),
            $input
        ),
        fn($carry, $item) => $carry + $item,
        0
    );
}

$getLineNumber = function ($line, $numbers) {
    $first = array_reduce(
        array_keys($numbers),
        function ($carry, $strNumber) use ($line, $numbers) {
            if (($pos = strpos($line, $strNumber)) !== false && $pos < $carry['position']) {
                $carry = ['position' => $pos, 'value' => $numbers[$strNumber]];
            }
            return $carry;
        },
        ['position' => strlen($line) + 1, 'value' => 0]
    )['value'];

    $last = array_reduce(
        array_keys($numbers),
        function ($carry, $strNumber) use ($line, $numbers) {
            if (($pos = strrpos($line, $strNumber)) !== false && $pos > $carry['position']) {
                $carry = ['position' => $pos, 'value' => $numbers[$strNumber]];
            }
            return $carry;
        },
        ['position' => -1, 'value' => 0]
    )['value'];

    return (int)($first . $last);
};

$input = file('input.txt', FILE_IGNORE_NEW_LINES);

echo 'part 1 : ', result($input, $getLineNumber), PHP_EOL;

$validValues = NUMBERS + ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
        'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9];

echo 'part 2 : ', result($input, $getLineNumber, $validValues), PHP_EOL;