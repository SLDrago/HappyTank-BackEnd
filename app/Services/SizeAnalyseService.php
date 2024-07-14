<?php

namespace App\Services;

use InvalidArgumentException;

class SizeAnalyseService
{
    protected $categories = [
        'A' => [0, 50],
        'B' => [50, 100],
        'C' => [100, 150],
        'D' => [150, 250],
        'E' => [250, 350],
        'F' => [350, PHP_INT_MAX],
    ];

    public function getCategory($average)
    {
        foreach ($this->categories as $category => $range) {
            if ($average >= $range[0] && $average < $range[1]) {
                return $category;
            }
        }
        return null;
    }

    public function getAverageSize($sizeRange)
    {
        $rangeStr = trim($sizeRange);

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
        $average = ($start + $end) / 2;

        return $average;
    }

    public function getCategoryDistance($cat1, $cat2)
    {
        $categories = array_keys($this->categories);
        $index1 = array_search($cat1, $categories);
        $index2 = array_search($cat2, $categories);
        return abs($index1 - $index2);
    }

    public function getMatchPercentageTwoSizes($size1, $size2)
    {
        $avg1 = $this->getAverageSize($size1);
        $avg2 = $this->getAverageSize($size2);

        $category1 = $this->getCategory($avg1);
        $category2 = $this->getCategory($avg2);

        $distance = $this->getCategoryDistance($category1, $category2);

        if ($distance == 0) {
            return 100;
        } elseif ($distance == 1) {
            return 75;
        } elseif ($distance == 2) {
            return 50;
        } else {
            return 0;
        }
    }

    public function getMatchPercentageThreeSizes($size1, $size2, $size3)
    {
        $avg1 = $this->getAverageSize($size1);
        $avg2 = $this->getAverageSize($size2);
        $avg3 = $this->getAverageSize($size3);

        $category1 = $this->getCategory($avg1);
        $category2 = $this->getCategory($avg2);
        $category3 = $this->getCategory($avg3);

        $distance1 = $this->getCategoryDistance($category1, $category2);
        $distance2 = $this->getCategoryDistance($category1, $category3);
        $distance3 = $this->getCategoryDistance($category2, $category3);

        $minDistance = min($distance1, $distance2, $distance3);

        if ($minDistance == 0) {
            return 100;
        } elseif ($minDistance == 1) {
            return 75;
        } elseif ($minDistance == 2) {
            return 50;
        } else {
            return 0;
        }
    }
}
