<?php

$input = array_map(
    str_split(...),
    file($argv[1], FILE_IGNORE_NEW_LINES)
);

function isSymbol($character)
{
    return !is_numeric($character) && $character !== '.';
}

$validPositions = array_map(
    function ($lineNumber) use ($input) {
        $validColumns = array_filter(
            array_keys($input[$lineNumber]),
            function ($colNumber) use ($input, $lineNumber) {
                return is_numeric($input[$lineNumber][$colNumber]) && (
                        isSymbol($input[$lineNumber][$colNumber - 1] ?? '.') ||
                        isSymbol($input[$lineNumber][$colNumber + 1] ?? '.') ||
                        isSymbol($input[$lineNumber - 1][$colNumber - 1] ?? '.') ||
                        isSymbol($input[$lineNumber - 1][$colNumber] ?? '.') ||
                        isSymbol($input[$lineNumber - 1][$colNumber + 1] ?? '.') ||
                        isSymbol($input[$lineNumber + 1][$colNumber - 1] ?? '.') ||
                        isSymbol($input[$lineNumber + 1][$colNumber] ?? '.') ||
                        isSymbol($input[$lineNumber + 1][$colNumber + 1] ?? '.')
                    );
            }
        );
        return $validColumns;
    },
    array_keys($input)
);

$validNumbers = [];
$validNumberPositions = [];
foreach ($validPositions as $lineId => $line) {
    $usedPositions = [];
    foreach ($line as $columnId => $column) {
        if (in_array($column, $usedPositions)) {
            continue;
        }
        $currentNumber = $input[$lineId][$column];
        $numberPosition = ['start' => $column, 'end' => $column];
        //numbers before?
        $next = $column - 1;
        while (is_numeric($input[$lineId][$next] ?? '.')) {
            $currentNumber = $input[$lineId][$next] . $currentNumber;
            $numberPosition['start'] = $next;
            $next--;
        }
        //numbers after?
        $next = $column + 1;
        while (is_numeric($input[$lineId][$next] ?? '.')) {
            $currentNumber .= $input[$lineId][$next];
            $numberPosition['end'] = $next;
            if ($columnId + $next - $column === $next) {
                $usedPositions[] = $next;
            }
            $next++;
        }
        $validNumbers[] = $currentNumber;
        $validNumberPositions[$lineId][] = ['position' => $numberPosition, 'value' => $currentNumber];
        $usedPositions[] = $column;
    }
}

echo 'part 1  : ', array_sum($validNumbers), PHP_EOL;

$ratios = [];
foreach ($input as $lineId => $line) {
    foreach ($line as $columnId => $char) {
        if ($char !== '*') {
            continue;
        }
        $adjacentNumbers = [];
        foreach (range(-1, 1) as $lineOffset) {
            foreach ($validNumberPositions[$lineId + $lineOffset] ?? [] as $numberPosition) {
                if ($columnId + ($lineOffset === 0 ? 1 : 0) >= $numberPosition['position']['start'] - abs($lineOffset) &&
                    $columnId - ($lineOffset === 0 ? 1 : 0) <= $numberPosition['position']['end'] + abs($lineOffset)) {
                    $adjacentNumbers[] = $numberPosition['value'];
                }
            }
        }
        if (count($adjacentNumbers) == 2) {
            $ratios[] = $adjacentNumbers[0] * $adjacentNumbers[1];
        }
    }
}

echo 'part 2  : ', array_sum($ratios), PHP_EOL;