<?php

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$times = [...array_filter(explode(' ', trim(explode(':', $input[0])[1])), fn($item) => $item !== '')];
$distances = [...array_filter(explode(' ', trim(explode(':', $input[1])[1])), fn($item) => $item !== '')];

$winners = array_map(
    function ($raceId) use ($times, $distances) {
        $availableTime = (int)$times[$raceId];
        $recordToBeat = (int)$distances[$raceId];
        return array_filter(
            array_map(
                function ($timeHeld) use ($availableTime) {
                    return $timeHeld * ($availableTime - $timeHeld);
                },
                range(1, $availableTime - 1)
            ),
            function ($distance) use ($recordToBeat) {
                return $recordToBeat < $distance;
            }
        );
    },
    array_keys($times)
);

echo 'part 1 : ', array_reduce(
    array_map(fn($winners) => count($winners), $winners),
    function ($carry, $item) {
        return $carry * $item;
    },
    1
), PHP_EOL;

//part2
$time = (int)implode($times);
$distance = (int)implode($distances);

$firstWinner = 1;
$lastWinner = $time - 1;
$found = false;

while ($firstWinner * ($time - $firstWinner) <= $distance) {
    $firstWinner++;
}
while ($lastWinner * ($time - $lastWinner) <= $distance) {
    $lastWinner--;
}
echo 'part 2 : ', $lastWinner - $firstWinner + 1, PHP_EOL;