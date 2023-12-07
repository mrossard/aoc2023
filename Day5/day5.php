<?php

function readMappings(array $input): array
{
    $from = 'seed';
    $target = 'soil';
    return array_reduce(
        array   : $input,
        callback: function ($carry, $line) use (&$from, &$target) {
            if (trim($line) === '') {
                return $carry;
            }
            if (str_ends_with($line, 'map:')) {
                [$from, $dummy, $target] = explode('-', explode(' ', $line)[0]);
            } else {
                [$destinationStart, $sourceStart, $range] = explode(' ', $line);
                $carry[$from][$target][] = [
                    'destination' => [
                        'start' => (int)$destinationStart,
                        'end' => (int)$destinationStart + (int)$range - 1,
                    ],
                    'source' => [
                        'start' => (int)$sourceStart,
                        'end' => (int)$sourceStart + (int)$range - 1,
                    ],
                    'range' => (int)$range,
                ];

            }

            return $carry;
        },
        initial : []
    );
}

function getSeedLocation($seed, $mappings): int
{
    $current = 'seed';
    $currentValue = $seed;
    while ($current != 'location') {
        $next = key($mappings[$current]);
        $nextValue = null;
        foreach ($mappings[$current][$next] as $mapping) {
            if ($currentValue >= $mapping['source']['start'] && $currentValue <= $mapping['source']['end']) {
                $nextValue = $mapping['destination']['start'] + ($currentValue - $mapping['source']['start']);
            }
        }
        if (null !== $nextValue) {
            $currentValue = $nextValue;
        }
        $current = $next;
    }
    return $currentValue;
}

$input = file($argv[1], FILE_IGNORE_NEW_LINES);
$seeds = array_map(fn($seed) => (int)$seed, explode(' ', trim(explode(':', array_shift($input))[1])));
$mappings = readMappings($input);

foreach ($seeds as $seed) {
    $locations[$seed] = getSeedLocation($seed, $mappings);
}

echo 'part 1 : ', min(...$locations ?? []), PHP_EOL;

/**
 * Part 2 - naïve version
 *
 * The "real" solution should involve collapsing mappings into soil-to-location mappings. No time for this.
 *
 * */
$seedsRanges = array_chunk($seeds, 2);

$min = PHP_INT_MAX;
foreach ($seedsRanges as $range) {
    for ($seed = $range[0]; $seed < $range[0] + $range[1]; $seed++) {
        if (($location = getSeedLocation($seed, $mappings)) < $min) {
            $min = $location;
        }
    }
}

echo 'part 2 : ', $min, PHP_EOL;
