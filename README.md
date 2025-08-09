# Laravel AI Log Analyzer - Proof of Concept

A Laravel 10 application that demonstrates AI integration for backend log and SQL query analysis using Google Gemini API.

## 🎯 Features

- **AI-powered log analysis**: Explain Laravel logs in plain English
- **Log classification**: Automatically classify logs as Error, Warning, or Info
- **SQL query explanation**: Get human-readable explanations of SQL queries
- **SQL optimization**: Receive performance and security improvement suggestions
- **Slack integration**: Fetch and analyze logs from Slack channels (optional)

## 🚀 Quick Start

### Prerequisites

- PHP 8.1+
- Composer
- Google Gemini API key - **Recommended**
- OpenAI API key (optional - alternative to Gemini)
- Ollama (optional - for local AI processing)

### Installation

1. **Clone and setup the project:**
   ```bash
   git clone <repository-url>
   cd laravel-ai-log-analyzer
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure environment:**
   ```bash
   cp env.example .env
   php artisan key:generate
   ```

4. **Configure Gemini (Recommended):**
   - Get your API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Add to `.env`:
     ```env
     GEMINI_API_KEY=your-gemini-api-key-here
     ```

5. **Configure your environment in `.env`:**
   ```env
   # Gemini Configuration (Recommended)
   GEMINI_API_KEY=your-gemini-api-key-here
   
   # OpenAI Configuration (Optional - if not using Gemini)
   OPENAI_API_KEY=your-openai-api-key-here
   
   # Ollama Configuration (Optional - for local development)
   OLLAMA_BASE_URL=http://localhost:11434
   OLLAMA_MODEL=llama2
   
   # Optional: Slack integration
   SLACK_BOT_TOKEN=your-slack-bot-token-here
   SLACK_CHANNEL_ID=your-slack-channel-id-here
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

## 📡 API Endpoints

All endpoints accept POST requests with JSON payloads and return JSON responses.

### 1. Explain Log
**Endpoint:** `POST /api/ai/explain-log`

**Request:**
```json
{
    "text": "[2024-08-01 11:25:33] production.ERROR: Undefined variable $user in /app/Http/Controllers/UserController.php:45"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "original_log": "[2024-08-01 11:25:33] production.ERROR: Undefined variable $user...",
        "explanation": "This is a PHP error indicating that the variable $user is being used without being defined first..."
    }
}
```

### 2. Classify Log
**Endpoint:** `POST /api/ai/classify-log`

**Request:**
```json
{
    "text": "[2024-08-01 11:25:33] production.ERROR: Undefined variable $user in /app/Http/Controllers/UserController.php:45"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "original_log": "[2024-08-01 11:25:33] production.ERROR: Undefined variable $user...",
        "classification": "Error: This is a PHP fatal error where an undefined variable is being accessed..."
    }
}
```

### 3. Explain SQL
**Endpoint:** `POST /api/ai/explain-sql`

**Request:**
```json
{
    "query": "SELECT u.name, u.email, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.created_at >= '2024-01-01' GROUP BY u.id, u.name, u.email HAVING order_count > 0 ORDER BY order_count DESC"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "original_query": "SELECT u.name, u.email, COUNT(o.id) as order_count...",
        "explanation": "This query retrieves user information along with their order counts. It joins the users and orders tables..."
    }
}
```

### 4. Optimize SQL
**Endpoint:** `POST /api/ai/optimize-sql`

**Request:**
```json
{
    "query": "SELECT * FROM users WHERE email = 'user@example.com'"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "original_query": "SELECT * FROM users WHERE email = 'user@example.com'",
        "optimization": "Consider adding an index on the email column for better performance. Also, avoid using SELECT * and specify only needed columns..."
    }
}
```

### 5. Fetch Slack Logs (Optional)
**Endpoint:** `GET /api/slack/fetch-logs?limit=10`

**Response:**
```json
{
    "success": true,
    "data": {
        "total_messages": 5,
        "logs": [
            {
                "original_message": "[ERROR] Database connection failed",
                "timestamp": "1640995200",
                "user": "U123456789",
                "ai_explanation": "This error indicates that the application cannot connect to the database..."
            }
        ]
    }
}
```

## 🏗️ Project Structure

```
app/
├── Http/Controllers/
│   ├── AiLogController.php      # Log analysis endpoints
│   ├── AiSqlController.php      # SQL analysis endpoints
│   ├── SlackLogController.php   # Slack integration
│   └── Controller.php           # Base controller
├── Services/
│   ├── GeminiService.php        # Gemini API integration
│   ├── OpenAiService.php        # OpenAI API integration (alternative)
│   └── SlackLogReaderService.php # Slack API integration
config/
├── services.php                 # API credentials configuration
routes/
├── api.php                      # API route definitions
└── web.php                      # Web route definitions
```

## 🔧 Configuration

### Gemini Configuration (Recommended)
- Get your API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
- Add to `.env`:
  ```env
  GEMINI_API_KEY=your-gemini-api-key-here
  ```

### OpenAI Configuration (Alternative)
- Get your API key from [OpenAI Platform](https://platform.openai.com/api-keys)
- Add to `.env`: `OPENAI_API_KEY=your-key-here`

### Ollama Configuration (Optional - for local development)
- Install Ollama from [ollama.ai](https://ollama.ai)
- Pull a model: `ollama pull llama2`
- Start the service: `ollama serve`
- Configure in `.env`:
  ```env
  OLLAMA_BASE_URL=http://localhost:11434
  OLLAMA_MODEL=llama2
  ```

### Slack Configuration (Optional)
1. Create a Slack app at [api.slack.com](https://api.slack.com/apps)
2. Add bot token scopes: `channels:history`, `channels:read`
3. Install the app to your workspace
4. Add to `.env`:
   ```env
   SLACK_BOT_TOKEN=xoxb-your-bot-token
   SLACK_CHANNEL_ID=C1234567890
   ```

## 🧪 Testing

Test the endpoints using curl or any API client:

```bash
# Test log explanation
curl -X POST http://localhost:8000/api/ai/explain-log \
  -H "Content-Type: application/json" \
  -d '{"text": "[ERROR] Undefined variable $user"}'

# Test SQL explanation
curl -X POST http://localhost:8000/api/ai/explain-sql \
  -H "Content-Type: application/json" \
  -d '{"query": "SELECT * FROM users WHERE id = 1"}'
```

### Test with Gemini Demo
Run the included test script to see Gemini in action:

```bash
php test_api.php
```

## 🔒 Security Notes

- Keep API keys secure in `.env` file
- Never commit API keys to version control
- Consider rate limiting for production use
- Validate all input data (already implemented)

## 🚀 Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure proper database settings
4. Set up HTTPS
5. Configure proper logging
6. Consider adding authentication middleware

## 📝 License

This is a proof of concept project. Use at your own risk in production environments.

## 🤝 Contributing

This is a demo project, but feel free to fork and improve it for your own use cases. 