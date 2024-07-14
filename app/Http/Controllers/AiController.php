<?php

namespace App\Http\Controllers;

use App\Models\Fishdata;
use App\Models\FishImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function getFishNameFromImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $fishNames = Fishdata::all()->map(function ($fish) {
            return $fish->common_name;
        })->implode(', ');

        $imagePath = $request->file('image')->getPathName();

        $base64Image = base64_encode(file_get_contents($imagePath));

        $message = "Here are the names of possible fish: $fishNames. Please provide the exact name of the fish in the image if it matches any of the given names. If the fish is not in the list, please provide the name of the fish as you identify. Give me just the name. If the image doesn't contain a fish please type 'Image doesn't match with the Database!'.";

        $payload = [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,$base64Image",
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 300,
        ];

        $response = Http::withOptions(['verify' => 'C:\Users\dilsh\OneDrive\Documents\cert\cacert.pem'])->withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("OpenAI_API_KEY"),
        ])->post('https://api.openai.com/v1/chat/completions', $payload);

        if ($response->successful()) {
            $responseData = $response->json();

            Log::info('OpenAI Vision response: ' . json_encode($responseData));

            $identifiedFishName = $responseData['choices'][0]['message']['content'] ?? 'Image identification failed!';

            // Check if the identified fish is in the database
            $fishData = Fishdata::where('common_name', 'like', "%$identifiedFishName%")
                ->first();

            if ($fishData) {
                $fishImage = FishImage::where('fish_id', $fishData->id)->first();

                $imageUrl = $fishImage ? $fishImage->image : null;

                $responseData = $fishData->toArray();
                $responseData['image'] = $imageUrl;

                return response()->json([
                    'status' => 'success',
                    'fish_data' => $responseData,
                ], 200);
            } else {
                return response()->json([
                    'fish_name' => $identifiedFishName,
                    'status' => 'not_found'
                ], 200);
            }
        } else {
            Log::error('OpenAI API error: ' . $response->body());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to communicate with the OpenAI API',
            ], $response->status());
        }
    }
}
