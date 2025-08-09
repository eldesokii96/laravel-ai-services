<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GeminiService;

class AiLogController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Explain a Laravel log snippet in plain English
     */
    public function explainLog(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000'
        ]);

        try {
            $logText = $request->input('text');
            $prompt = "Explain this Laravel error log:\n{$logText}";
            
            $explanation = $this->geminiService->generateResponse($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'original_log' => $logText,
                    'explanation' => $explanation
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Classify a Laravel log as Error, Warning, or Info
     */
    public function classifyLog(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000'
        ]);

        try {
            $logText = $request->input('text');
            $prompt = "Classify this Laravel log as Error, Warning, or Info and explain why:\n{$logText}";
            
            $classification = $this->geminiService->generateResponse($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'original_log' => $logText,
                    'classification' => $classification
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to classify log: ' . $e->getMessage()
            ], 500);
        }
    }
} 