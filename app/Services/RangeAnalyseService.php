<?php

namespace App\Services;

use InvalidArgumentException;

class RangeAnalyseService
{
    /**
     * Parse a temperature range string into an array of floats.
     */
    public function parseRange(string $rangeStr): array
    {
        $rangeStr = trim($rangeStr);

        $rangeStr = str_replace(['–', '—', '-'], '–', $rangeStr);

        $parts = explode("–", $rangeStr);

        $parts = array_map('trim', $parts);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException("Invalid range string format.");
        }

        if (!is_numeric($parts[0]) || !is_numeric($parts[1])) {
            throw new InvalidArgumentException("Range values must be numeric.");
        }

        $start = floatval($parts[0]);
        $end = floatval($parts[1]);

        return [$start, $end];
    }



    /**
     * Calculate the overlap percentage and range for three temperature ranges.
     */
    public function calculateOverlap(string $range1Str, string $range2Str): array
    {
        list($range1Start, $range1End) = $this->parseRange($range1Str);
        list($range2Start, $range2End) = $this->parseRange($range2Str);

        if ($range1Start > $range1End || $range2Start > $range2End) {
            throw new InvalidArgumentException("Invalid range input.");
        }

        $overlapStart = max($range1Start, $range2Start);
        $overlapEnd = min($range1End, $range2End);

        $overlapLength = max(0, $overlapEnd - $overlapStart);

        $range1Length = $range1End - $range1Start;
        $range2Length = $range2End - $range2Start;

        $totalLength = max($range1End, $range2End) - min($range1Start, $range2Start);

        $overlapPercentage = ($overlapLength / $totalLength) * 100;

        if ($overlapLength > 0) {
            return [
                'overlap_percentage' => $overlapPercentage,
                'overlap_range' => [$overlapStart, $overlapEnd]
            ];
        } else {
            return [
                'overlap_percentage' => 0,
                'overlap_range' => null
            ];
        }
    }


    public function calculateOverlapThreeRanges(string $range1Str, string $range2Str, string $range3Str): array
    {
        list($range1Start, $range1End) = $this->parseRange($range1Str);
        list($range2Start, $range2End) = $this->parseRange($range2Str);
        list($range3Start, $range3End) = $this->parseRange($range3Str);

        if ($range1Start > $range1End || $range2Start > $range2End || $range3Start > $range3End) {
            throw new InvalidArgumentException("Invalid range input.");
        }

        $overlapStart = max($range1Start, $range2Start, $range3Start);
        $overlapEnd = min($range1End, $range2End, $range3End);

        $overlapLength = max(0, $overlapEnd - $overlapStart);

        $range1Length = $range1End - $range1Start;
        $range2Length = $range2End - $range2Start;
        $range3Length = $range3End - $range3Start;

        $totalLength = max($range1End, $range2End, $range3End) - min($range1Start, $range2Start, $range3Start);

        $overlapPercentage = ($overlapLength / $totalLength) * 100;

        if ($overlapLength > 0) {
            return [
                'overlap_percentage' => $overlapPercentage,
                'overlap_range' => [$overlapStart, $overlapEnd]
            ];
        } else {
            return [
                'overlap_percentage' => 0,
                'overlap_range' => null
            ];
        }
    }
}
