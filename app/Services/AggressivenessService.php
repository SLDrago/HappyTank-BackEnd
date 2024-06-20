<?php

namespace App\Services;

class AggressivenessService
{
    /**
     * Get the aggressiveness percentage based on two inputs.
     *
     * @param string $input1
     * @param string $input2
     * @return string
     * Aggressive-Own
     * Not-Aggressive
     * Aggressive-Small
     * Aggressive
     */
    public function getAggressivenessPercentage($input1, $input2)
    {
        if ($input1 == "Aggressive" && $input2 == "Aggressive") {
            return 0;
        } elseif ($input1 == "Not-Aggressive" && $input2 == "Not-Aggressive") {
            return 100;
        } elseif ($input1 == "Aggressive-Small" && $input2 == "Aggressive-Small") {
            return 75;
        } elseif (($input1 == "Aggressive" && $input2 == "Not-Aggressive") || ($input1 == "Not-Aggressive" && $input2 == "Aggressive")) {
            return 0;
        } elseif (($input1 == "Aggressive-Small" && $input2 == "Not-Aggressive") || ($input1 == "Not-Aggressive" && $input2 == "Aggressive-Small")) {
            return 50;
        } else {
            return "Invalid inputs";
        }
    }
    /**
     * Get the aggressiveness percentage based on three inputs.
     *
     * @param string $input1
     * @param string $input2
     * @param string $input3
     * @return string
     */
    public function getAggressivenessPercentageThree($input1, $input2, $input3)
    {
        $inputs = [$input1, $input2, $input3];
        $countAggressive = count(array_filter($inputs, fn ($input) => $input == "Aggressive"));
        $countAggressiveSmall = count(array_filter($inputs, fn ($input) => $input == "Aggressive-Small"));
        $countNotAggressive = count(array_filter($inputs, fn ($input) => $input == "Not-Aggressive"));

        if ($countAggressive == 3) {
            return 0;
        } elseif ($countNotAggressive == 3) {
            return 100;
        } elseif ($countAggressiveSmall == 3) {
            return 75;
        } elseif ($countAggressive == 2 && $countNotAggressive == 1) {
            return 0;
        } elseif ($countAggressiveSmall == 2 && $countNotAggressive == 1) {
            return 50;
        } elseif ($countAggressive == 1 && $countNotAggressive == 2) {
            return 0;
        } elseif ($countAggressiveSmall == 1 && $countNotAggressive == 2) {
            return 50;
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
