<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fishdata;
use App\Models\FishImage;
use App\Services\RangeAnalyseService;
use App\Services\SizeAnalyseService;
use App\Services\AggressivenessService;
use App\Http\Controllers\FishImageController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;



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
            'commonName' => 'required|string|max:255',
            'scientificName' => 'required|string|max:255',
            'aquariumSize' => 'required|string',
            'habitat' => 'nullable|string|max:1000',
            'maxStandardLengthMax' => 'required|numeric|min:1',
            'maxStandardLengthMin' => 'required|numeric|min:1|lt:maxStandardLengthMax',
            'temperatureMax' => 'required|numeric|min:1',
            'temperatureMin' => 'required|numeric|min:1|lt:temperatureMax',
            'phMax' => 'required|numeric|min:1',
            'phMin' => 'required|numeric|min:1|lt:phMax',
            'diet' => 'required|string|max:255',
            'behavior' => 'required|string|max:255',
            'sexualDimorphysm' => 'required|string|max:500',
            'reproduction' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $fishData = [
            'common_name' => $request->commonName,
            'scientific_name' => $request->scientificName,
            'aquarium_size' => $request->aquariumSize,
            'habitat' => $request->habitat,
            'max_standard_length' => $request->maxStandardLengthMin . '-' . $request->maxStandardLengthMax,
            'temperature' => $request->temperatureMin . '-' . $request->temperatureMax,
            'ph' => $request->phMin . '-' . $request->phMax,
            'diet' => $request->diet,
            'behavior' => $request->behavior,
            'sexual_dimorphisms' => $request->sexualDimorphysm,
            'reproduction' => $request->reproduction,
            'notes' => $request->notes,
        ];

        // Save the fish record
        $fish = $this->fish->create($fishData);

        // Add fish_id to the request
        $request->merge(['fish_id' => $fish->id]);

        // Call the image upload function and pass the modified request
        $imageController = new FishImageController();
        $result = $imageController->store($request);

        // Handle image upload failure
        if (!$result) {
            $fish->delete();
            return response()->json(['message' => 'Failed to upload image. Fish data not saved.'], 500);
        }

        return response()->json(['message' => 'Fish added successfully']);
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
            'id' => 'required|integer',
        ])['id'];

        $fish = $this->fish->find($id);

        if (!$fish) {
            return response()->json(['message' => 'Fish not found'], 404);
        }

        if ($request->hasFile('image')) {
            if ($fish->image) {
                $oldImagePath = public_path($fish->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $image = $request->validate([
                'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imagePath = $request->file('image')->store('fish_images', 'public');
            $fish->image = '/storage/' . $imagePath;
        }

        $fish->update($data);

        return response()->json(['message' => 'Fish Updated Successfully']);
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

        $fishImage = FishImage::where('fish_id', $id)->first();

        if ($fishImage) {
            $relativePath = str_replace('/storage/', '', $fishImage->image);
            Storage::disk('public')->delete($relativePath);

            $fishImage->delete();
        }

        $fish->delete();

        return response()->json(['message' => 'Fish and associated image deleted successfully']);
    }


    public function getFishByIdWithImages(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer'
        ]);
        $id = $data['id'];

        $fish = $this->fish->find($id);

        if (!$fish) {
            return response()->json(['message' => 'Fish not found'], 404);
        }

        $imageUrl = optional($fish->fishImages->first())->image;

        return response()->json(
            $fish
                ->makeHidden('fishImages')
                ->toArray() + ['image' => $imageUrl]
        );
    }

    public function getSelectedFishCompatibility(Request $request)
    {
        $fishes = $this->fish->all();
        $data = [];
        $result = 0;

        if ($request->input('fish1') != null && $request->input('fish2') != null) {
            $fish1 = $fishes->firstWhere('id', $request->input('fish1'));
            $fish2 = $fishes->firstWhere('id', $request->input('fish2'));

            if ($request->input('fish3') != null) {
                $fish3 = $fishes->firstWhere('id', $request->input('fish3'));
                $data['peacepercentage'] = $this->aggressivenessService->getAggressivenessPercentageThree(
                    $fish1->behavior,
                    $fish2->behavior,
                    $fish3->behavior,
                    $fish1->max_standard_length,
                    $fish2->max_standard_length,
                    $fish3->max_standard_length
                );
                $data['tempOverlap'] = $this->rangeAnalyseService->calculateOverlapThreeRanges(
                    $fish1->temperature,
                    $fish2->temperature,
                    $fish3->temperature
                );
                $data['lengthMatchPrecentage'] = $this->sizeAnalyseService->getMatchPercentageThreeSizes(
                    $fish1->max_standard_length,
                    $fish2->max_standard_length,
                    $fish3->max_standard_length
                );
                $data['phOverlap'] = $this->rangeAnalyseService->calculateOverlapThreeRanges(
                    $fish1->ph,
                    $fish2->ph,
                    $fish3->ph
                );
            } else {
                $data['peacepercentage'] = $this->aggressivenessService->getAggressivenessPercentage(
                    $fish1->behavior,
                    $fish2->behavior,
                    $fish1->max_standard_length,
                    $fish2->max_standard_length
                );
                $data['tempOverlap'] = $this->rangeAnalyseService->calculateOverlap(
                    $fish1->temperature,
                    $fish2->temperature
                );
                $data['lengthMatchPrecentage'] = $this->sizeAnalyseService->getMatchPercentageTwoSizes(
                    $fish1->max_standard_length,
                    $fish2->max_standard_length
                );
                $data['phOverlap'] = $this->rangeAnalyseService->calculateOverlap(
                    $fish1->ph,
                    $fish2->ph
                );
            }

            if ($data['phOverlap']['overlap_percentage'] > 0 && $data['tempOverlap']['overlap_percentage'] > 0) {
                $result = (($data['peacepercentage'] * 2) + $data['tempOverlap']['overlap_percentage'] + ($data['lengthMatchPrecentage'] * 2) + $data['phOverlap']['overlap_percentage']) / 6;
            } else {
                $result = 0;
            }

            return response()->json([
                'result' => $result,
                'tempOverlapRange' => $data['tempOverlap']['overlap_range'],
                'phOverlapRange' => $data['phOverlap']['overlap_range'],
                'peacePercentage' => $data['peacepercentage'],
                'tempMatchPercentage' => $data['tempOverlap']['overlap_percentage'],
                'lengthMatchPercentage' => $data['lengthMatchPrecentage'],
                'phMatchPercentage' => $data['phOverlap']['overlap_percentage'],
                'fish1Name' => $fish1->common_name,
                'fish2Name' => $fish2->common_name,
                'fish3Name' => isset($fish3) ? $fish3->common_name : null
            ], 200);
        } else {
            return response()->json(['error' => 'Please provide at least two fish IDs.'], 400);
        }
    }
}
