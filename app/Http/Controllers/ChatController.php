<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'message' => 'required|string',
        ]);

        // Log the incoming message for debugging purposes (optional)
        Log::info('Incoming message: ' . $request->input('message'));

        // Call the OpenAI API
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("CHATGPT_API_Key")
        ])->post('https://api.openai.com/v1/chat/completions', [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are an assistant specialized in fish-related topics. Only respond to questions that are about fish. When answering, make sure your responses are simple and easy to understand, suitable for a 15-year-old. Use a friendly and approachable tone."
                ],
                [
                    "role" => "user",
                    "content" => $request->input('message')
                ]
            ],
            "max_tokens" => 256,
            "temperature" => 0.5
        ]);

        // Check if the request to OpenAI was successful
        if ($response->successful()) {
            // Extract the response content
            $responseData = $response->json();

            // Log the response for debugging purposes (optional)
            Log::info('OpenAI response: ' . json_encode($responseData));

            // Return the response as JSON
            return response()->json([
                'status' => 'success',
                'data' => $responseData,
            ], 200);
        } else {
            // Log the error response for debugging purposes (optional)
            Log::error('OpenAI API error: ' . $response->body());

            // Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to communicate with the OpenAI API',
            ], $response->status());
        }
    }
}
