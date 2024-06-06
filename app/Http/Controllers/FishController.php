<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fishdata;
use App\Services\FishCompatibility;
use App\Services\RangeAnalyseService;
use App\Services\SizeAnalyseService;
use App\Services\AggressivenessService;


class FishController extends Controller
{

    protected $fish;
    protected $compatibilityService;
    protected $rangeAnalyseService;
    protected $sizeAnalyseService;
    protected $aggressivenessService;

    public function __construct()
    {
        $this->fish = new FishData();
        $this->compatibilityService = new FishCompatibility();
        $this->rangeAnalyseService = new RangeAnalyseService();
        $this->sizeAnalyseService = new SizeAnalyseService();
        $this->aggressivenessService = new AggressivenessService();
    }

    public function showFish()
    {
        $fishes = $this->fish->all();
        return view('test')->with('fishes', $fishes);
    }

    public function selectedFish(Request $request)
    {
        $fishes = $this->fish->all();

        $fish1 = $fishes->firstWhere('id', $request->input('fish1'));
        $fish2 = $fishes->firstWhere('id', $request->input('fish2'));
        $fish3 = $fishes->firstWhere('id', $request->input('fish3'));

        // $result = $this->compatibilityService->checkCompatibility($fish1, $fish2, $fish3);
        $data = [];
        $data['peacepercentage'] = $this->aggressivenessService->getAggressivenessPercentageThree($fish1->behavior, $fish2->behavior, $fish3->behavior);
        $data['tempOverlap'] = $this->rangeAnalyseService->calculateOverlapThreeRanges($fish1->temperature, $fish2->temperature, $fish3->temperature);
        $data['lengthMatchPrecentage'] = $this->sizeAnalyseService->getMatchPercentageThreeSizes($fish1->max_standard_length, $fish2->max_standard_length, $fish3->max_standard_length);
        $data['phOverlap'] = $this->rangeAnalyseService->calculateOverlapThreeRanges($fish1->ph, $fish2->ph, $fish3->ph);

        $result = ($data['peacepercentage'] + $data['tempOverlap']['overlap_percentage'] + $data['lengthMatchPrecentage'] + $data['phOverlap']['overlap_percentage']) / 4;

        return view('test')->with([
            'fishes' => $fishes,
            'selectedFish1' => $fish1,
            'selectedFish2' => $fish2,
            'selectedFish3' => $fish3,
            'data' => $data,
            'result' => $result
        ]);
    }

    public function getSelectedFishCompatibility(Request $request)
    {
        $fishes = $this->fish->all();

        $fish1 = $fishes->firstWhere('id', $request->input('fish1'));
        $fish2 = $fishes->firstWhere('id', $request->input('fish2'));
        $fish3 = $fishes->firstWhere('id', $request->input('fish3'));

        $data = [];
        $data['peacepercentage'] = $this->aggressivenessService->getAggressivenessPercentageThree($fish1->behavior, $fish2->behavior, $fish3->behavior);
        $data['tempOverlap'] = $this->rangeAnalyseService->calculateOverlapThreeRanges($fish1->temperature, $fish2->temperature, $fish3->temperature);
        $data['lengthMatchPrecentage'] = $this->sizeAnalyseService->getMatchPercentageThreeSizes($fish1->max_standard_length, $fish2->max_standard_length, $fish3->max_standard_length);
        $data['phOverlap'] = $this->rangeAnalyseService->calculateOverlapThreeRanges($fish1->ph, $fish2->ph, $fish3->ph);

        $result = ($data['peacepercentage'] + $data['tempOverlap']['overlap_percentage'] + $data['lengthMatchPrecentage'] + $data['phOverlap']['overlap_percentage']) / 4;

        return response()->json([
            'result' => $result,
            'tempOverlapRange' => $data['tempOverlap']['overlap_range'],
            'phOverlapRange' => $data['phOverlap']['overlap_range'],
            'fish1Habitat' => $fish1->habitat,
            'fish2Habitat' => $fish2->habitat,
            'fish3Habitat' => $fish3->habitat
        ], 200);
    }
}
