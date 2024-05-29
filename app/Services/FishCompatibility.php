<?php
namespace App\Services;

class FishCompatibility
{
    protected $thresholds = [
        'size' => 20,
        'compatibility' => 1,
        'aquarium_size' => 20000,
        'ph' => 0.5,
        'temperature' => 3

    ];

    public function isCompatible($fish1, $fish2, $fish3)
    {
        return
               $this->isWithinThreshold($fish1->size, $fish2->size, $fish3->size, 'size') &&
               $this->isWithinThreshold($fish1->compatibility, $fish2->compatibility, $fish3->compatibility, 'compatibility') &&
               $this->isWithinThreshold($fish1->aquarium_size, $fish2->aquarium_size, $fish3->aquarium_size, 'aquarium_size') &&
               $this->isWithinThreshold($fish1->ph, $fish2->ph, $fish3->ph, 'ph')&&
               $this->isWithinThreshold($fish1->temperature, $fish2->temperature, $fish3->temperature, 'temperature') ;
    }

    protected function isWithinThreshold($value1, $value2, $value3, $attribute)
    {
        $threshold = $this->thresholds[$attribute];
        return abs($value1 - $value2) <= $threshold &&
               abs($value1 - $value3) <= $threshold &&
               abs($value2 - $value3) <= $threshold;
    }

    public function checkCompatibility($fish1, $fish2, $fish3)
    {
        if (!$this->isCompatible($fish1, $fish2, $fish3)) {
            return "Fish are not compatible.";
        }
        return "Fish are compatible.";
    }
}
