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
    protected $rangeAnalyseService;
    protected $sizeAnalyseService;
    protected $aggressivenessService;

    public function __construct()
    {
        $this->fish = new FishData();
        $this->rangeAnalyseService = new RangeAnalyseService();
        $this->sizeAnalyseService = new SizeAnalyseService();
        $this->aggressivenessService = new AggressivenessService();
    }

    public function getFishNames()
    {
        $fishes = $this->fish->all(['id', 'common_name', 'scientific_name']);

        $fishData = $fishes->map(function ($fish) {
            return [
                'id' => $fish->id,
                'common_name' => $fish->common_name,
                'scientific_name' => $fish->scientific_name,
                'formatted_name' => $fish->common_name . ' (' . $fish->scientific_name . ')',
            ];
        });

        return response()->json($fishData);
    }

    public function getFishById(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer'
        ]);
        $id = $data['id'];

        $fish = $this->fish->find($id);

        if (!$fish) {
            return response()->json(['message' => 'Fish not found'], 404);
        }

        return response()->json($fish);
    }

    public function addFish(Request $request)
    {
        $request->validate([
            'common_name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
            'aquarium_size' => 'required|string|max:255',
            'habitat' => 'string|max:1000',
            'max_standard_length' => 'required|string|max:255',
            'temperature' => 'required|string|max:255',
            'ph' => 'required|string|max:255',
            'diet' => 'required|string|max:255',
            'behavior' => 'required|string|max:255|in:Aggressive,Aggressive-Small,Not-Aggressive',
            'sexual_dimorphisms' => 'required|string|max:500',
            'reproduction' => 'required|string|max:1000',
            'notes' => 'required|string|max:1000',
        ]);

        $fish = $this->fish->create($request->all());

        return response()->json($fish, 201);
    }

    public function updateFish(Request $request)
    {
        $data = $request->validate([
            'common_name' => 'sometimes|required|string|max:255',
            'scientific_name' => 'sometimes|required|string|max:255',
            'aquarium_size' => 'sometimes|required|string|max:255',
            'habitat' => 'sometimes|required|string|max:1000',
            'max_standard_length' => 'sometimes|required|string|max:255',
            'temperature' => 'sometimes|required|string|max:255',
            'ph' => 'sometimes|required|string|max:255',
            'diet' => 'sometimes|required|string|max:255',
            'behavior' => 'sometimes|required|string|max:255|in:Aggressive,Aggressive-Small,Not-Aggressive',
            'sexual_dimorphisms' => 'sometimes|required|string|max:500',
            'reproduction' => 'sometimes|required|string|max:1000',
            'notes' => 'sometimes|required|string|max:1000',
        ]);

        $id = $request->validate([
            'id' => 'required|integer'
        ])['id'];

        $fish = $this->fish->find($id);

        if (!$fish) {
            return response()->json(['message' => 'Fish not found'], 404);
        }
        $fish->update($data);

        return response()->json($fish);
    }

    public function removeFish(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer'
        ]);

        $id = $data['id'];
        $fish = $this->fish->find($id);

        if (!$fish) {
            return response()->json(['message' => 'Fish not found'], 404);
        }

        $fish->delete();

        return response()->json(['message' => 'Fish deleted successfully']);
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
            'fish1Name' => $fish1->common_name,
            'fish2Name' => $fish2->common_name,
            'fish3Name' => $fish3->common_name
        ], 200);
    }
}
