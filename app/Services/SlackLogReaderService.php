<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackLogReaderService
{
    protected string $botToken;
    protected string $channelId;

    public function __construct()
    {
        $this->botToken = config('services.slack.bot_token', '');
        $this->channelId = config('services.slack.channel_id', '');
    }

    public function fetchLogs(): array
    {
        // If no Slack configuration, return empty array
        if (empty($this->botToken) || empty($this->channelId)) {
            return [
                'success' => false,
                'message' => 'Slack configuration not found. Please set SLACK_BOT_TOKEN and SLACK_CHANNEL_ID in your .env file.',
                'logs' => []
            ];
        }

        try {
            $response = Http::withToken($this->botToken)
                ->get('https://slack.com/api/conversations.history', [
                    'channel' => $this->channelId,
                    'limit' => 10
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['ok']) {
                    $messages = $data['messages'] ?? [];
                    $logs = [];
                    
                    foreach ($messages as $message) {
                        $logs[] = [
                            'timestamp' => $message['ts'] ?? '',
                            'text' => $message['text'] ?? '',
                            'user' => $message['user'] ?? 'unknown'
                        ];
                    }
                    
                    return [
                        'success' => true,
                        'logs' => $logs
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Slack API error: ' . ($data['error'] ?? 'Unknown error'),
                        'logs' => []
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to connect to Slack API',
                    'logs' => []
                ];
            }
        } catch (\Exception $e) {
            Log::error('Slack API error', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Error connecting to Slack: ' . $e->getMessage(),
                'logs' => []
            ];
        }
    }
} 