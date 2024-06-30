<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'required|array', // Add this line to validate the conversation history
        ]);

        Log::info('Incoming message: ' . $request->input('message'));
        Log::info('Conversation history: ' . json_encode($request->input('history')));

        // System message to set the context
        $systemMessage = [
            "role" => "system",
            "content" => "You are an assistant specialized in fish-related topics. Only respond to questions that are about fish. When answering, make sure your responses are simple and easy to understand, suitable for a 15-year-old. Use a friendly and approachable tone."
        ];

        // Add the user's new message to the conversation history
        $newMessage = [
            "role" => "user",
            "content" => $request->input('message'),
        ];

        // Merge the system message, history, and the new message
        $messages = array_merge([$systemMessage], $request->input('history'), [$newMessage]);

        $response = Http::withOptions(['verify' => 'C:\Users\dilsh\OneDrive\Documents\cert\cacert.pem'])->withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("CHATGPT_API_KEY")
        ])->post('https://api.openai.com/v1/chat/completions', [
            "model" => "gpt-3.5-turbo",
            "messages" => $messages,
            "max_tokens" => 256,
            "temperature" => 0.5
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            Log::info('OpenAI response: ' . json_encode($responseData));

            return response()->json([
                'status' => 'success',
                'data' => $responseData,
            ], 200);
        } else {
            Log::error('OpenAI API error: ' . $response->body());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to communicate with the OpenAI API',
            ], $response->status());
        }
    }


    public function getFishTankRecommendations(Request $request)
    {
        $request->validate([
            'fish_names' => 'required|string',
        ]);

        Log::info('Incoming fish names: ' . $request->input('fish_names'));

        $fishNames = $request->input('fish_names');
        $message = "Do the following fish match with each other: $fishNames? What kind of tank environment will be suitable for them?";

        $response = Http::withOptions(['verify' => 'C:\Users\dilsh\OneDrive\Documents\cert\cacert.pem'])->withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("CHATGPT_API_KEY")
        ])->post('https://api.openai.com/v1/chat/completions', [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are an assistant specialized in fish-related topics. Only respond to questions that are about fish. When answering, make sure your responses are simple and easy to understand, suitable for a 15-year-old. Use a friendly and approachable tone."
                ],
                [
                    "role" => "user",
                    "content" => $message
                ]
            ],
            "max_tokens" => 256,
            "temperature" => 0.5
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            Log::info('OpenAI response: ' . json_encode($responseData));

            $aiMessage = $responseData['choices'][0]['message']['content'] ?? 'No response from AI';

            return response()->json([
                'status' => 'success',
                'message' => $aiMessage,
            ], 200);
        } else {
            Log::error('OpenAI API error: ' . $response->body());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to communicate with the OpenAI API',
            ], $response->status());
        }
    }
}
