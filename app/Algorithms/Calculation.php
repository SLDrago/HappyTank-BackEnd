<?php
namespace App\Algorithms;



function isSizeCompatible($fish1, $fish2, $fish3) {
    $threshold = 20;
    return (
        abs($fish1['size'] - $fish2['size']) <= $threshold &&
        abs($fish1['size'] - $fish3['size']) <= $threshold &&
        abs($fish2['size'] - $fish3['size']) <= $threshold
    );
}

function isAggressivenessCompatible($fish1, $fish2, $fish3) {
    $threshold = 1;
    return (
        abs($fish1['aggressiveness'] - $fish2['aggressiveness']) <= $threshold &&
        abs($fish1['aggressiveness'] - $fish3['aggressiveness']) <= $threshold &&
        abs($fish2['aggressiveness'] - $fish3['aggressiveness']) <= $threshold
    );
}


function isTankSizeCompatible($fish1, $fish2, $fish3) {
    $threshold = 20000;
    return (
        abs($fish1['tank_size'] - $fish2['tank_size']) <= $threshold &&
        abs($fish1['tank_size'] - $fish3['tank_size']) <= $threshold &&
        abs($fish2['tank_size'] - $fish3['tank_size']) <= $threshold
    );
}


function isPHCompatible($fish1, $fish2, $fish3) {
    $threshold = 0.5;
    return (
        abs($fish1['ph'] - $fish2['ph']) <= $threshold &&
        abs($fish1['ph'] - $fish3['ph']) <= $threshold &&
        abs($fish2['ph'] - $fish3['ph']) <= $threshold
    );
}

function isTemperatureCompatible($fish1, $fish2, $fish3) {
    $threshold = 3;
    return (
        abs($fish1['temperature'] - $fish2['temperature']) <= $threshold &&
        abs($fish1['temperature'] - $fish3['temperature']) <= $threshold &&
        abs($fish2['temperature'] - $fish3['temperature']) <= $threshold
    );
}


public function fishCompatibilityCheck($fish1, $fish2, $fish3){
if(!$this->isSizeCompatible($fish1, $fish2, $fish3)){
    echo "This Fish Combination is not suitable";
}

if(!$this->isAggressivenessCompatible($fish1, $fish2, $fish3)){
    echo "This Fish Combination is not suitable";
}

if(!$this->isTankSizeCompatible($fish1, $fish2, $fish3)){
    echo "This Fish Combination is not suitable";
}
if(!$this->isPHCompatible($fish1, $fish2, $fish3)){
    echo "This Fish Combination is not suitable";
}
if(!$this->isTemperatureCompatible($fish1, $fish2, $fish3)){
    echo "This Fish Combination is not suitable";
}
return "fish are compatible";
}


