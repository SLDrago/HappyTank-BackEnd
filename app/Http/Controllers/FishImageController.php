<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FishImage;
use Illuminate\Support\Facades\Storage;
use Exception;

class FishImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fish_id' => 'required|exists:fishdatas,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check if there is already a record for this fish_id in fishimages
        $fishImageExists = FishImage::where('fish_id', $request->fish_id)->exists();

        if ($fishImageExists) {
            return response()->json(['error' => 'An image already exists for this fish.'], 400);
        }

        try {
            // Store file locally and log file path
            $imagePath = $request->file('image')->store('fish_images', 'public');
            if ($imagePath) {

                // Generate public URL for the file and log URL
                $imageUrl = Storage::url($imagePath);

                // Save the new image record
                FishImage::create([
                    'fish_id' => $request->fish_id,
                    'image' => $imageUrl,
                ]);

                return response()->json(['success' => 'You have successfully uploaded an image.']);
            } else {
                return response()->json(['error' => 'Failed to store the image.'], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to store the image.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function getfishImageByFishId(Request $request)
    {
        $request->validate([
            'fish_id' => 'required|exists:fishdatas,id',
        ]);

        $fishImage = FishImage::where('fish_id', $request->fish_id)->first();

        if ($fishImage) {
            return response()->json(['image' => $fishImage->image]);
        } else {
            return response()->json(['error' => 'No image found for this fish.'], 404);
        }
    }
}
