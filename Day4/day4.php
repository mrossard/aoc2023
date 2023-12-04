<?php

$cards = array_map(
    function ($line) {
        [$winning, $yours] = explode('|', explode(':', str_replace('  ', ' ', $line))[1]);
        return [
            'winning' => array_map(fn($str) => (int)$str, explode(' ', trim($winning))),
            'yours' => array_map(fn($str) => (int)$str, explode(' ', trim($yours))),
        ];
    },
    file($argv[1], FILE_IGNORE_NEW_LINES)
);

$matches = array_map(
    static fn($card) => array_intersect($card['winning'], $card['yours']),
    $cards
);

echo 'part 1 : ', array_reduce($matches,
    static function ($carry, $card) {
        if (count($card) === 0) {
            return $carry;
        }
        return $carry + bindec('1' . str_repeat('0', count($card) - 1));
    },
    0
), PHP_EOL;

$countCopies = array_reduce(
    array_keys($matches),
    static function ($carry, $cardId) use ($matches) {
        $matchCount = count($matches[$cardId]);
        for ($times = 0; $times < $carry[$cardId]; $times++) {
            for ($i = 1; $i <= $matchCount; $i++) {
                $carry[$cardId + $i] = ($carry[$cardId + $i]) + 1; //no matches on last card, don't handle the case.
            }
        }
        return $carry;
    },
    array_fill(0, count($cards), 1)
);

echo 'part 2 : ', array_sum($countCopies), PHP_EOL;