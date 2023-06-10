<?php

namespace Macocci7;

class Combination
{
    public function getAllCombinations($items) {
        if (!is_array($items)) return;
        if (empty($items)) return;
        $count = count($items);
        $numberOfAllPatterns = 2 ** $count;
        $bitPatterns = [];
        $format = '%0' . $count . 'b';
        for ($i = 1; $i < $numberOfAllPatterns; $i++) {
            $bitPatterns[] = sprintf($format, $i);
        }
        $combinations = [];
        foreach($bitPatterns as $bits) {
            $combination = [];
            foreach(str_split($bits) as $index => $bit) {
                if ((bool) $bit) $combination[] = $items[$index];
            }
            $combinations[] = $combination;
        }
        return $combinations;
    }

    public function pairs($items)
    {
        if (!is_array($items)) return;
        if (count($items) < 2) return;
        $pairs = [];
        $lastIndex = count($items) - 1;
        for ($x = 0; $x < $lastIndex; $x++) {
            for ($y = $x + 1; $y <= $lastIndex; $y++) {
                $pairs[] = [$items[$x], $items[$y]];
            }
        }
        return $pairs;
    }
}
