<?php
// app/Services/TemperatureRangeService.php

namespace App\Services;

use InvalidArgumentException;

class RangeAnalyseService
{
    /**
     * Parse a temperature range string into an array of floats.
     *
     * @param string $rangeStr
     * @return array
     * @throws InvalidArgumentException
     */
    public function parseRange(string $rangeStr): array
    {
        // Normalize the input string
        $rangeStr = trim($rangeStr);

        // Replace different types of dashes with a single delimiter
        $rangeStr = str_replace(['–', '—', '-'], '–', $rangeStr); // en dash, em dash, hyphen to en dash

        // Split the range string by the en dash
        $parts = explode("–", $rangeStr);

        // Trim any whitespace around the parts
        $parts = array_map('trim', $parts);

        // Check if we have exactly 2 parts
        if (count($parts) !== 2) {
            throw new InvalidArgumentException("Invalid range string format.");
        }

        // Convert the parts to float and ensure they are valid numbers
        if (!is_numeric($parts[0]) || !is_numeric($parts[1])) {
            throw new InvalidArgumentException("Range values must be numeric.");
        }

        $start = floatval($parts[0]);
        $end = floatval($parts[1]);

        return [$start, $end];
    }



    /**
     * Calculate the overlap percentage and range for three temperature ranges.
     *
     * @param string $range1Str
     * @param string $range2Str
     * @param string $range3Str
     * @return array
     * @throws InvalidArgumentException
     */
    public function calculateOverlap(string $range1Str, string $range2Str): array
    {
        // Parse the range strings into numeric values
        list($range1Start, $range1End) = $this->parseRange($range1Str);
        list($range2Start, $range2End) = $this->parseRange($range2Str);

        // Ensure the ranges are valid
        if ($range1Start > $range1End || $range2Start > $range2End) {
            throw new InvalidArgumentException("Invalid range input.");
        }

        // Find the start and end of the overlap
        $overlapStart = max($range1Start, $range2Start);
        $overlapEnd = min($range1End, $range2End);

        // Calculate the overlap length
        $overlapLength = max(0, $overlapEnd - $overlapStart);

        // Calculate the length of the ranges
        $range1Length = $range1End - $range1Start;
        $range2Length = $range2End - $range2Start;

        // Determine the length of the range used for the percentage calculation
        $totalLength = max($range1End, $range2End) - min($range1Start, $range2Start);

        // Calculate the percentage overlap
        $overlapPercentage = ($overlapLength / $totalLength) * 100;

        // Return the overlap percentage and the overlapping range
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
        // Parse the range strings into numeric values
        list($range1Start, $range1End) = $this->parseRange($range1Str);
        list($range2Start, $range2End) = $this->parseRange($range2Str);
        list($range3Start, $range3End) = $this->parseRange($range3Str);

        // Ensure the ranges are valid
        if ($range1Start > $range1End || $range2Start > $range2End || $range3Start > $range3End) {
            throw new InvalidArgumentException("Invalid range input.");
        }

        // Find the start and end of the overlap among the three ranges
        $overlapStart = max($range1Start, $range2Start, $range3Start);
        $overlapEnd = min($range1End, $range2End, $range3End);

        // Calculate the overlap length
        $overlapLength = max(0, $overlapEnd - $overlapStart);

        // Calculate the length of the ranges
        $range1Length = $range1End - $range1Start;
        $range2Length = $range2End - $range2Start;
        $range3Length = $range3End - $range3Start;

        // Determine the length of the range used for the percentage calculation
        $totalLength = max($range1End, $range2End, $range3End) - min($range1Start, $range2Start, $range3Start);

        // Calculate the percentage overlap
        $overlapPercentage = ($overlapLength / $totalLength) * 100;

        // Return the overlap percentage and the overlapping range
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
