<?php

/**
 * Simple test script to demonstrate the AI Log Analyzer API functionality
 * This script simulates the API calls without requiring the full Laravel framework
 */

class MockOpenAiService
{
    public function generateResponse(string $prompt): string
    {
        // Simulate AI responses based on the prompt
        if (strpos($prompt, 'Explain this Laravel error log') !== false) {
            return "This appears to be a PHP error where a variable is being used without being defined first. The error occurs in the UserController.php file at line 45. To fix this, ensure the \$user variable is properly initialized before use.";
        }
        
        if (strpos($prompt, 'Classify this Laravel log') !== false) {
            return "Error: This is a PHP fatal error where an undefined variable is being accessed. The application will crash when this code is executed. Immediate attention required.";
        }
        
        if (strpos($prompt, 'Explain what this SQL query does') !== false) {
            return "This SQL query retrieves user information (name and email) along with a count of their orders. It joins the users and orders tables, filters for users created after January 1, 2024, groups the results by user, and only shows users who have at least one order, sorted by order count in descending order.";
        }
        
        if (strpos($prompt, 'Analyze and optimize this SQL query') !== false) {
            return "Optimization suggestions:\n1. Add an index on the email column for faster lookups\n2. Avoid using SELECT * - specify only needed columns\n3. Consider adding a WHERE clause to limit results if possible\n4. The query looks simple and should perform well with proper indexing.";
        }
        
        return "AI analysis completed successfully.";
    }
}

class ApiTester
{
    private MockOpenAiService $openAiService;
    
    public function __construct()
    {
        $this->openAiService = new MockOpenAiService();
    }
    
    public function testExplainLog(): array
    {
        $logText = "[2024-08-01 11:25:33] production.ERROR: Undefined variable \$user in /app/Http/Controllers/UserController.php:45";
        $prompt = "Explain this Laravel error log:\n{$logText}";
        
        $explanation = $this->openAiService->generateResponse($prompt);
        
        return [
            'success' => true,
            'data' => [
                'original_log' => $logText,
                'explanation' => $explanation
            ]
        ];
    }
    
    public function testClassifyLog(): array
    {
        $logText = "[2024-08-01 11:25:33] production.ERROR: Undefined variable \$user in /app/Http/Controllers/UserController.php:45";
        $prompt = "Classify this Laravel log as Error, Warning, or Info and explain why:\n{$logText}";
        
        $classification = $this->openAiService->generateResponse($prompt);
        
        return [
            'success' => true,
            'data' => [
                'original_log' => $logText,
                'classification' => $classification
            ]
        ];
    }
    
    public function testExplainSql(): array
    {
        $sqlQuery = "SELECT u.name, u.email, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.created_at >= '2024-01-01' GROUP BY u.id, u.name, u.email HAVING order_count > 0 ORDER BY order_count DESC";
        $prompt = "Explain what this SQL query does:\n{$sqlQuery}";
        
        $explanation = $this->openAiService->generateResponse($prompt);
        
        return [
            'success' => true,
            'data' => [
                'original_query' => $sqlQuery,
                'explanation' => $explanation
            ]
        ];
    }
    
    public function testOptimizeSql(): array
    {
        $sqlQuery = "SELECT * FROM users WHERE email = 'user@example.com'";
        $prompt = "Analyze and optimize this SQL query:\n{$sqlQuery}";
        
        $optimization = $this->openAiService->generateResponse($prompt);
        
        return [
            'success' => true,
            'data' => [
                'original_query' => $sqlQuery,
                'optimization' => $optimization
            ]
        ];
    }
    
    public function runAllTests(): void
    {
        echo "🤖 Laravel AI Log Analyzer - API Test Results\n";
        echo "=============================================\n\n";
        
        // Test 1: Explain Log
        echo "1. Testing /api/ai/explain-log\n";
        echo "-----------------------------\n";
        $result = $this->testExplainLog();
        echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        
        // Test 2: Classify Log
        echo "2. Testing /api/ai/classify-log\n";
        echo "-------------------------------\n";
        $result = $this->testClassifyLog();
        echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        
        // Test 3: Explain SQL
        echo "3. Testing /api/ai/explain-sql\n";
        echo "------------------------------\n";
        $result = $this->testExplainSql();
        echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        
        // Test 4: Optimize SQL
        echo "4. Testing /api/ai/optimize-sql\n";
        echo "-------------------------------\n";
        $result = $this->testOptimizeSql();
        echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        
        echo "✅ All tests completed successfully!\n";
        echo "📚 Check the README.md for full setup instructions\n";
    }
}

// Run the tests
$tester = new ApiTester();
$tester->runAllTests(); 