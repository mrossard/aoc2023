<?php

$histories = array_map(
    fn($line) => array_map(intval(...), explode(' ', $line)),
    file($argv[1], FILE_IGNORE_NEW_LINES)
);

function printHistory($rows)
{
    foreach ($rows as $row) {
        foreach ($row as $value) {
            echo $value, ' ';
        }
        echo PHP_EOL;
    }
    echo PHP_EOL;
}

function extrapolate(array $histories)
{
    foreach ($histories as $id => $history) {
        $currentRow = 1;
        $rows = [0 => $history];
        //drill down
        while (!empty(array_filter($rows[$currentRow - 1], static fn($item) => $item !== 0))) {
            for ($i = 1, $iMax = count($rows[$currentRow - 1]); $i < $iMax; $i++) {
                $difference = $rows[$currentRow - 1][$i] - $rows[$currentRow - 1][$i - 1];
                $rows[$currentRow][] = $difference;
            }
            $currentRow++;
        }
        //extrapolate back up
        $rows[$currentRow - 1][] = 0;
        for ($j = $currentRow - 2; $j >= 0; $j--) {
            $last = array_key_last($rows[$j]);
            $rows[$j][] = $rows[$j][$last] + $rows[$j + 1][$last];
        }
        $histories[$id][] = $rows[0][$last + 1];
    }
    return $histories;
}

echo 'part 1 : ', array_sum(array_map(static fn($history) => $history[array_key_last($history)], extrapolate($histories))), PHP_EOL;
$histories = array_map(array_reverse(...), $histories);
echo 'part 2 : ', array_sum(array_map(static fn($history) => $history[array_key_last($history)], extrapolate($histories))), PHP_EOL;