<?php

namespace App\Services;

/** CVSS v3.1 base score calculator (mirrors the JavaScript calculator on the report form). */
class CvssService
{
    public static function compute(array $m): array
    {
        $AV = ['N' => .85, 'A' => .62, 'L' => .55, 'P' => .2];
        $AC = ['L' => .77, 'H' => .44];
        $UI = ['N' => .85, 'R' => .62];
        $CIA = ['H' => .56, 'L' => .22, 'N' => 0.0];

        $changed = $m['s'] === 'C';
        $pr = ['N' => .85, 'L' => $changed ? .68 : .62, 'H' => $changed ? .5 : .27][$m['pr']];

        $iss = 1 - (1 - $CIA[$m['c']]) * (1 - $CIA[$m['i']]) * (1 - $CIA[$m['a']]);
        $impact = $changed ? 7.52 * ($iss - .029) - 3.25 * pow($iss - .02, 15) : 6.42 * $iss;
        $expl = 8.22 * $AV[$m['av']] * $AC[$m['ac']] * $pr * $UI[$m['ui']];

        $score = $impact <= 0 ? 0.0 : self::roundUp(min(($changed ? 1.08 : 1) * ($impact + $expl), 10));

        $severity = match (true) {
            $score == 0.0 => 'informative',
            $score < 4.0 => 'low',
            $score < 7.0 => 'medium',
            $score < 9.0 => 'high',
            default => 'critical',
        };

        $vector = sprintf('CVSS:3.1/AV:%s/AC:%s/PR:%s/UI:%s/S:%s/C:%s/I:%s/A:%s',
            $m['av'], $m['ac'], $m['pr'], $m['ui'], $m['s'], $m['c'], $m['i'], $m['a']);

        return [$vector, $score, $severity];
    }

    private static function roundUp(float $x): float
    {
        $i = (int) round($x * 100000);
        return $i % 10000 === 0 ? $i / 100000 : (floor($i / 10000) + 1) / 10;
    }
}
