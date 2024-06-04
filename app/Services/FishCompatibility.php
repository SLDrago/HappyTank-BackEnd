<?php
namespace App\Services;
class FishCompatibility
{
    public function checkCompatibility($fish1, $fish2, $fish3){
        if(!$this->isSizeCompatible($fish1, $fish2, $fish3)){
            return " This fish Combination is not suitable due to size difference";
        }
        if(!$this->isAggressivenessCompatible($fish1, $fish2, $fish3)){
            return "This Fish Combination is not suitable due to aggression difference";
        }
        // if(!$this->isTankCompatible($fish1, $fish2, $fish3)){
        //     return "This fish Combination is not suitable due to tank size differences";

        // }
        if(!$this->isPHCompatible($fish1, $fish2, $fish3)){
            return "This Fish Combination is not suitable due to PH differences";

        }
        if(!$this->isTemperatureCompatible($fish1, $fish2, $fish3)){
            return "This fish Combination is not suitable due to Temperature differences";
        }
        return "fish are compatible";
    }
    private function isSizeCompatible($fish1, $fish2, $fish3)
{
    $threshold = 20;
    return (
        abs($fish1->Max_Standard_Length - $fish2->Max_Standard_Length) <= $threshold &&
        abs($fish1->Max_Standard_Length - $fish3->Max_Standard_Length) <= $threshold &&
        abs($fish2->Max_Standard_Length - $fish3->Max_Standard_Length));
    }
    private function isAggressivenessCompatible($fish1, $fish2, $fish3)
    {
        $threshold = 1;
        return(abs($fish1->Behavior_Comapatibility - $fish2->Behavior_Comapatibility) <= $threshold &&
    $fish1->Behavior_Compatibility - $fish3->Behavior_Compatibility <= $threshold &&
    $fish2->Behavior_Compatibility - $fish3->Behavior_Compatibility <= $threshold);
    }
    private function isTankSizdeCompatible($fish1, $fish2, $fish3){
        $threshold = 20000;
        return (
            abs($fish1->Aquarium_Size - $fish2->Aquarium_Size) <=$threshold &&
            abs($fish1->Aquarium_Size - $fish3->Aquarium_Size) <= $threshold &&
            abs($fish2->Aquarium_Size - $fish3->Aquarium_Size) <=$threshold
        );

    }
    private function isPHCompatible($fish1, $fish2, $fish3)
    {
        $threshold = 0.5;
        return (
            abs($fish1->PH - $fish2->PH) <= $threshold &&
            abs($fish1->PH - $fish3->PH) <= $threshold &&
            abs($fish2->PH - $fish3->PH) <= $threshold
        );
    }
    private function isTemperatureCompatible($fish1, $fish2, $fish3)
    {
        $threshold = 3;
        return (
            abs($fish1->Temperature - $fish2->Temperature) <= $threshold &&
            abs($fish1->Temperature - $fish3->Temperature) <= $threshold &&
            abs($fish2->Temperature - $fish3->Temperature) <= $threshold
        );
    }
}
// class FishCompatibility
// {
//     protected $thresholds = [
//         'size' => 20,
//         'compatibility' => 1,
//         'aquarium_size' => 20000,
//         'ph' => 0.5,
//         'temperature' => 3

//     ];

//     public function isCompatible($fish1, $fish2, $fish3)
//     {
//         return
//                $this->isWithinThreshold($fish1->size, $fish2->size, $fish3->size, 'size') &&
//                $this->isWithinThreshold($fish1->compatibility, $fish2->compatibility, $fish3->compatibility, 'compatibility') &&
//                $this->isWithinThreshold($fish1->aquarium_size, $fish2->aquarium_size, $fish3->aquarium_size, 'aquarium_size') &&
//                $this->isWithinThreshold($fish1->ph, $fish2->ph, $fish3->ph, 'ph')&&
//                $this->isWithinThreshold($fish1->temperature, $fish2->temperature, $fish3->temperature, 'temperature') ;
//     }

//     protected function isWithinThreshold($value1, $value2, $value3, $attribute)
//     {
//         $threshold = $this->thresholds[$attribute];
//         return abs($value1 - $value2) <= $threshold &&
//                abs($value1 - $value3) <= $threshold &&
//                abs($value2 - $value3) <= $threshold;
//     }

//     public function checkCompatibility($fish1, $fish2, $fish3)
//     {
//         if (!$this->isCompatible($fish1, $fish2, $fish3)) {
//             return "Fish are not compatible.";
//         }
//         return "Fish are compatible.";
//     }
// }
