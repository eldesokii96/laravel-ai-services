<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.base_url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama2');
    }

    /**
     * Generate a response from Ollama API
     */
    public function generateResponse(string $prompt): string
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/api/generate', [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.3,
                    'top_p' => 0.9,
                    'max_tokens' => 1000
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['response'] ?? 'No response generated';
            } else {
                Log::error('Ollama API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Ollama API request failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Ollama service error', ['error' => $e->getMessage()]);
            
            // Return a fallback response if Ollama is not available
            return $this->getFallbackResponse($prompt);
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

    /**
     * Get available models from Ollama
     */
    public function getAvailableModels(): array
    {
        try {
            $response = Http::get($this->baseUrl . '/api/tags');
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['models'] ?? [];
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Ollama models', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Check if Ollama is running and accessible
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/tags');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Fallback responses when Ollama is not available
     */
    private function getFallbackResponse(string $prompt): string
    {
        if (strpos($prompt, 'Explain this Laravel error log') !== false) {
            return "This appears to be a Laravel error log. The system detected an issue that needs attention. Please check the log details and ensure proper error handling is in place.";
        }
        
        if (strpos($prompt, 'Classify this Laravel log') !== false) {
            return "Error: This log entry indicates a system error that requires immediate attention. The application may not function correctly until this issue is resolved.";
        }
        
        if (strpos($prompt, 'Explain what this SQL query does') !== false) {
            return "This SQL query performs a database operation. It retrieves or modifies data based on the specified conditions. Consider reviewing the query structure for optimization opportunities.";
        }
        
        if (strpos($prompt, 'Analyze and optimize this SQL query') !== false) {
            return "SQL Optimization Suggestions:\n1. Consider adding appropriate indexes\n2. Review the query structure for efficiency\n3. Ensure proper WHERE clauses are used\n4. Check for potential performance bottlenecks";
        }
        
        return "Analysis completed. Please ensure Ollama is running locally for full AI-powered analysis.";
    }
} 