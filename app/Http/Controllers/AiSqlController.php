<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GeminiService;

class AiSqlController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Explain what a SQL query does in human-readable format
     */
    public function explainSql(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:5000'
        ]);

        try {
            $sqlQuery = $request->input('query');
            $prompt = "Explain what this SQL query does:\n{$sqlQuery}";
            
            $explanation = $this->geminiService->generateResponse($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'original_query' => $sqlQuery,
                    'explanation' => $explanation
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to explain SQL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze and optimize a SQL query for performance and security
     */
    public function optimizeSql(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:5000'
        ]);

        try {
            $sqlQuery = $request->input('query');
            $prompt = "Analyze and optimize this SQL query:\n{$sqlQuery}";
            
            $optimization = $this->geminiService->generateResponse($prompt);

            return response()->json([
                'success' => true,
                'data' => [
                    'original_query' => $sqlQuery,
                    'optimization' => $optimization
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to optimize SQL: ' . $e->getMessage()
            ], 500);
        }
    }
} 