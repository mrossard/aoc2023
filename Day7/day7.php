<?php


function compareHands($hand1, $hand2): int
{
    return match (true) {
        $hand1['strength'] === $hand2['strength'] => $hand2['orderablehand'] <=> $hand1['orderablehand'],
        default => $hand1['strength'] <=> $hand2['strength']
    };
}

function getHandStrength(string $hand, bool $withJokers = false): int
{
    $cards = str_split($hand);
    $frequencies = array_reduce(
        array   : $cards,
        callback: function ($result, $card) {
            $result[$card] = ($result[$card] ?? 0) + 1;
            return $result;
        },
        initial : []
    );

    $jokersCount = 0;
    if ($withJokers) {
        $jokersCount = $frequencies['J'] ?? 0;
        if ($jokersCount == 5) {
            return 7;
        }
        unset($frequencies['J']);
    }
    $nonJokers = 5 - $jokersCount;
    $max = max($frequencies);

    //5 of a kind
    if ($max + $jokersCount == 5) {
        return 7;
    }
    //4 of a kind
    if ($max + $jokersCount == 4) {
        return 6;
    }
    //full house
    if (count($frequencies) < 3) {
        return 5;
    }
    //3 of a kind
    if ($max >= $nonJokers - 2) {
        return 4;
    }
    //2 pairs - max 1 joker!
    if (count(array_keys($frequencies, 2)) === (2 - $jokersCount)) {
        return 3;
    }
    //1 pair
    if (in_array(2, $frequencies) || $jokersCount == 1) {
        return 2;
    }

    return 1;
}

function translateHand(string $hand, $cardStrength)
{
    return implode(
        array_map(
            fn($char) => str_pad(array_keys($cardStrength, $char)[0], 2, '0', STR_PAD_LEFT),
            str_split($hand)
        )
    );
}

$withJoker = isset($argv[2]);
$cardStrengths = match (true) {
    $withJoker => ['A', 'K', 'Q', 'T', '9', '8', '7', '6', '5', '4', '3', '2', 'J'],
    default => ['A', 'K', 'Q', 'J', 'T', '9', '8', '7', '6', '5', '4', '3', '2']
};

$hands = array_map(
    function ($line) use ($withJoker, $cardStrengths) {
        [$hand, $bid] = explode(' ', $line);
        return [
            'hand' => $hand,
            'orderablehand' => translateHand($hand, $cardStrengths),
            'bid' => (int)$bid,
            'strength' => getHandStrength($hand, $withJoker),
        ];
    },
    file($argv[1], FILE_IGNORE_NEW_LINES)
);

usort($hands, compareHands(...));

echo 'Result : ', array_sum(
    array_map(
        fn($position) => ($position + 1) * $hands[$position]['bid'],
        array_keys($hands)
    )
), PHP_EOL;