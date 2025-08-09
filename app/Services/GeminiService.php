<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected string $model = 'gemini-1.5-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: getenv('GEMINI_API_KEY');
        
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API key not configured');
        }
    }

    /**
     * Generate a response from Gemini API
     */
    public function generateResponse(string $prompt): string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/models/' . $this->model . ':generateContent?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1000,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated';
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'error_message' => $errorMessage
                ]);
                
                if ($response->status() === 429) {
                    throw new \Exception('Gemini API rate limit exceeded. Please try again in a few minutes.');
                }
                
                throw new \Exception('Gemini API request failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Gemini service error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Set the model to use for API calls
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }
} 