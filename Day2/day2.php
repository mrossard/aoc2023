<?php

function maxPerColor(array $input)
{
    return array_map(
        fn($line) => [
            'game' => (int)explode(' ', explode(':', $line)[0])[1],
            'values' => array_reduce(
                explode(',', str_replace(';', ',', explode(':', $line))[1]),
                function ($carry, $item) {
                    [$value, $color] = explode(' ', trim($item));
                    if ((int)$value > $carry[$color]) {
                        $carry[$color] = $value;
                    }
                    return $carry;
                },
                ['red' => 0, 'blue' => 0, 'green' => 0]
            )
        ],
        $input
    );
}

$input = file($argv[1], FILE_IGNORE_NEW_LINES);
$bagContents = ['red' => $argv[2], 'green' => $argv[3], 'blue' => $argv[4],];
$maxPerColor = maxPerColor($input);
$possibleGames = array_filter(
    $maxPerColor,
    function ($game) use ($bagContents) {
        return array_reduce(
            array_keys($bagContents),
            function ($carry, $color) use ($bagContents, $game) {
                return $carry && $game['values'][$color] <= $bagContents[$color];
            },
            true
        );
    }
);

echo 'part 1 : ', array_sum(array_map(fn($game) => $game['game'], $possibleGames)), PHP_EOL;

echo 'part 2 : ', array_sum(
    array_map(
        fn($game) => $game['values']['red'] * $game['values']['green'] * $game['values']['blue'],
        $maxPerColor
    )
), PHP_EOL;