<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\SlackLogReaderService;
use App\Services\GeminiService;

class SlackLogController extends Controller
{
    protected SlackLogReaderService $slackService;
    protected GeminiService $geminiService;

    public function __construct(SlackLogReaderService $slackService, GeminiService $geminiService)
    {
        $this->slackService = $slackService;
        $this->geminiService = $geminiService;
    }

    /**
     * Fetch recent logs from Slack channel and analyze them with AI
     */
    public function fetchLogs(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            
            // Fetch recent messages from Slack
            $messages = $this->slackService->fetchRecentMessages($limit);
            
            $analyzedLogs = [];
            
            foreach ($messages as $message) {
                $prompt = "Explain this Laravel error log:\n{$message['text']}";
                $explanation = $this->geminiService->generateResponse($prompt);
                
                $analyzedLogs[] = [
                    'original_message' => $message['text'],
                    'timestamp' => $message['timestamp'],
                    'user' => $message['user'] ?? 'Unknown',
                    'ai_explanation' => $explanation
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_messages' => count($analyzedLogs),
                    'logs' => $analyzedLogs
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch and analyze Slack logs: ' . $e->getMessage()
            ], 500);
        }
    }
} 