<?php

namespace App\Services;

class AggressivenessService
{
    public function getAggressivenessPercentage($input1, $input2, $size1 = null, $size2 = null)
    {
        $sizeAnalyseService = new SizeAnalyseService();
        $size1 = $sizeAnalyseService->getAverageSize($size1);
        $size2 = $sizeAnalyseService->getAverageSize($size2);

        if ($input1 == "Aggressive" && $input2 == "Aggressive") {
            return 0;
        } elseif ($input1 == "Not-Aggressive" && $input2 == "Not-Aggressive") {
            return 100;
        } elseif ($input1 == "Aggressive-Small" && $input2 == "Aggressive-Small") {
            if ($size1 && $size2) {
                return $size1 == $size2 ? 55 : 45;
            } else {
                return 55;
            }
        } elseif (($input1 == "Aggressive" && $input2 == "Not-Aggressive") || ($input1 == "Not-Aggressive" && $input2 == "Aggressive")) {
            return 0;
        } elseif (($input1 == "Aggressive-Small" && $input2 == "Not-Aggressive") || ($input1 == "Not-Aggressive" && $input2 == "Aggressive-Small")) {
            if ($size1 && $size2) {
                if ($size1 > $size2) {
                    return 50;
                } else {
                    return 70;
                }
            } else {
                return 60;
            }
        } elseif (($input1 == "Aggressive" && $input2 == "Aggressive-Small") || ($input1 == "Aggressive-Small" && $input2 == "Aggressive")) {
            return 25;
        } else {
            return "Invalid inputs";
        }
    }

    public function getAggressivenessPercentageThree($input1, $input2, $input3, $size1 = null, $size2 = null, $size3 = null)
    {
        $sizeAnalyseService = new SizeAnalyseService();
        $size1 = $sizeAnalyseService->getAverageSize($size1);
        $size2 = $sizeAnalyseService->getAverageSize($size2);
        $size3 = $sizeAnalyseService->getAverageSize($size3);

        $inputs = [$input1, $input2, $input3];
        $sizes = [$size1, $size2, $size3];
        $countAggressive = count(array_filter($inputs, fn ($input) => $input == "Aggressive"));
        $countAggressiveSmall = count(array_filter($inputs, fn ($input) => $input == "Aggressive-Small"));
        $countNotAggressive = count(array_filter($inputs, fn ($input) => $input == "Not-Aggressive"));

        if ($countAggressive == 3) {
            return 0;
        } elseif ($countNotAggressive == 3) {
            return 100;
        } elseif ($countAggressiveSmall == 3) {
            if ($sizes[0] && $sizes[1] && $sizes[2]) {
                return ($sizes[0] == $sizes[1] && $sizes[1] == $sizes[2]) ? 55 : 45;
            } else {
                return 55;
            }
        } elseif ($countAggressive == 2 && $countNotAggressive == 1) {
            return 0;
        } elseif ($countAggressiveSmall == 2 && $countNotAggressive == 1) {
            if ($sizes[0] && $sizes[1] && $sizes[2]) {
                if ($sizes[0] == $sizes[1] || $sizes[1] == $sizes[2] || $sizes[0] == $sizes[2]) {
                    return 50;
                } else {
                    return 45;
                }
            } else {
                return 50;
            }
        } elseif ($countAggressive == 1 && $countNotAggressive == 2) {
            return 0;
        } elseif ($countAggressiveSmall == 1 && $countNotAggressive == 2) {
            if ($sizes[0] && $sizes[1] && $sizes[2]) {
                return min($sizes) > max($sizes) ? 70 : 50;
            } else {
                return 50;
            }
        } elseif ($countAggressive == 2 && $countAggressiveSmall == 1) {
            return 25;
        } elseif ($countAggressiveSmall == 2 && $countAggressive == 1) {
            return 25;
        } elseif ($countAggressive == 1 && $countAggressiveSmall == 1 && $countNotAggressive == 1) {
            return 33;
        } else {
            return "Invalid inputs";
        }
    }
}
