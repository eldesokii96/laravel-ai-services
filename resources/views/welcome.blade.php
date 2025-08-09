<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel AI Log Analyzer - Powered by Gemini</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div x-data="aiAnalyzer()" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-white mb-4">
                <i class="fas fa-robot text-blue-300"></i>
                Laravel AI Log Analyzer
            </h1>
            <p class="text-xl text-blue-100 mb-2">Powered by Google Gemini AI</p>
            <p class="text-blue-200">Intelligent analysis for Laravel logs and SQL queries</p>
        </div>

        <!-- Main Content -->
        <div class="grid lg:grid-cols-2 gap-8 max-w-7xl mx-auto">
            
            <!-- Log Analysis Section -->
            <div class="glass-effect rounded-2xl p-6">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <i class="fas fa-file-alt text-blue-300 mr-3"></i>
                    Log Analysis
                </h2>
                
                <!-- Log Explanation -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-100 mb-3">Explain Log</h3>
                    <textarea 
                        x-model="logText" 
                        placeholder="Paste your Laravel log here...&#10;Example: [2024-08-01 11:25:33] production.ERROR: Undefined variable $user"
                        class="w-full h-32 p-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200 resize-none focus:outline-none focus:ring-2 focus:ring-blue-300"
                    ></textarea>
                    <button 
                        @click="explainLog()" 
                        :disabled="loading.log"
                        class="mt-3 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-500 text-white rounded-lg font-medium transition-colors flex items-center"
                    >
                        <i class="fas fa-magic mr-2"></i>
                        <span x-text="loading.log ? 'Analyzing...' : 'Explain Log'"></span>
                    </button>
                    
                    <div x-show="results.log" x-transition class="mt-4 p-4 bg-white/10 rounded-lg">
                        <h4 class="font-semibold text-blue-100 mb-2">Analysis Result:</h4>
                        <div x-html="results.log" class="text-white text-sm leading-relaxed"></div>
                    </div>
                </div>

                <!-- Log Classification -->
                <div>
                    <h3 class="text-lg font-semibold text-blue-100 mb-3">Classify Log</h3>
                    <textarea 
                        x-model="classifyText" 
                        placeholder="Paste your log for classification...&#10;Example: [2024-08-01 11:25:33] production.ERROR: Undefined variable $user"
                        class="w-full h-32 p-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200 resize-none focus:outline-none focus:ring-2 focus:ring-blue-300"
                    ></textarea>
                    <button 
                        @click="classifyLog()" 
                        :disabled="loading.classify"
                        class="mt-3 px-6 py-2 bg-green-500 hover:bg-green-600 disabled:bg-gray-500 text-white rounded-lg font-medium transition-colors flex items-center"
                    >
                        <i class="fas fa-tags mr-2"></i>
                        <span x-text="loading.classify ? 'Classifying...' : 'Classify Log'"></span>
                    </button>
                    
                    <div x-show="results.classify" x-transition class="mt-4 p-4 bg-white/10 rounded-lg">
                        <h4 class="font-semibold text-blue-100 mb-2">Classification Result:</h4>
                        <div x-html="results.classify" class="text-white text-sm leading-relaxed"></div>
                    </div>
                </div>
            </div>

            <!-- SQL Analysis Section -->
            <div class="glass-effect rounded-2xl p-6">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <i class="fas fa-database text-green-300 mr-3"></i>
                    SQL Analysis
                </h2>
                
                <!-- SQL Explanation -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-blue-100 mb-3">Explain SQL</h3>
                    <textarea 
                        x-model="sqlQuery" 
                        placeholder="Paste your SQL query here...&#10;Example: SELECT u.name, u.email, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.created_at >= '2024-01-01' GROUP BY u.id, u.name, u.email HAVING order_count > 0 ORDER BY order_count DESC"
                        class="w-full h-32 p-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200 resize-none focus:outline-none focus:ring-2 focus:ring-blue-300"
                    ></textarea>
                    <button 
                        @click="explainSql()" 
                        :disabled="loading.sql"
                        class="mt-3 px-6 py-2 bg-purple-500 hover:bg-purple-600 disabled:bg-gray-500 text-white rounded-lg font-medium transition-colors flex items-center"
                    >
                        <i class="fas fa-search mr-2"></i>
                        <span x-text="loading.sql ? 'Analyzing...' : 'Explain SQL'"></span>
                    </button>
                    
                    <div x-show="results.sql" x-transition class="mt-4 p-4 bg-white/10 rounded-lg">
                        <h4 class="font-semibold text-blue-100 mb-2">SQL Explanation:</h4>
                        <div x-html="results.sql" class="text-white text-sm leading-relaxed"></div>
            </div>
        </div>

                <!-- SQL Optimization -->
                <div>
                    <h3 class="text-lg font-semibold text-blue-100 mb-3">Optimize SQL</h3>
                    <textarea 
                        x-model="optimizeQuery" 
                        placeholder="Paste your SQL query for optimization...&#10;Example: SELECT * FROM users WHERE email = 'user@example.com'"
                        class="w-full h-32 p-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200 resize-none focus:outline-none focus:ring-2 focus:ring-blue-300"
                    ></textarea>
                    <button 
                        @click="optimizeSql()" 
                        :disabled="loading.optimize"
                        class="mt-3 px-6 py-2 bg-orange-500 hover:bg-orange-600 disabled:bg-gray-500 text-white rounded-lg font-medium transition-colors flex items-center"
                    >
                        <i class="fas fa-rocket mr-2"></i>
                        <span x-text="loading.optimize ? 'Optimizing...' : 'Optimize SQL'"></span>
                    </button>
                    
                    <div x-show="results.optimize" x-transition class="mt-4 p-4 bg-white/10 rounded-lg">
                        <h4 class="font-semibold text-blue-100 mb-2">Optimization Suggestions:</h4>
                        <div x-html="results.optimize" class="text-white text-sm leading-relaxed"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Examples Section -->
        <div class="mt-12 max-w-4xl mx-auto">
            <div class="glass-effect rounded-2xl p-6">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-300 mr-3"></i>
                    Quick Examples
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-100 mb-3">Sample Logs</h3>
                        <div class="space-y-2">
                            <button @click="loadExample('log1')" class="block w-full text-left p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-white text-sm">
                                <strong>Error:</strong> Undefined variable $user
                            </button>
                            <button @click="loadExample('log2')" class="block w-full text-left p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-white text-sm">
                                <strong>Warning:</strong> Database connection timeout
                            </button>
                            <button @click="loadExample('log3')" class="block w-full text-left p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-white text-sm">
                                <strong>Info:</strong> User login successful
                            </button>
            </div>
        </div>

                    <div>
                        <h3 class="text-lg font-semibold text-blue-100 mb-3">Sample SQL Queries</h3>
                        <div class="space-y-2">
                            <button @click="loadExample('sql1')" class="block w-full text-left p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-white text-sm">
                                <strong>Complex:</strong> User orders with aggregation
                            </button>
                            <button @click="loadExample('sql2')" class="block w-full text-left p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-white text-sm">
                                <strong>Simple:</strong> User lookup by email
                            </button>
                            <button @click="loadExample('sql3')" class="block w-full text-left p-3 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-white text-sm">
                                <strong>Join:</strong> Products with categories
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-blue-200">
            <p class="mb-2">
                <i class="fas fa-code mr-2"></i>
                Built with Laravel 10 + Google Gemini AI
            </p>
            <p class="text-sm">
                <i class="fas fa-shield-alt mr-2"></i>
                Secure API integration with rate limiting
            </p>
        </div>
    </div>

    <script>
        function aiAnalyzer() {
            return {
                logText: '',
                classifyText: '',
                sqlQuery: '',
                optimizeQuery: '',
                loading: {
                    log: false,
                    classify: false,
                    sql: false,
                    optimize: false
                },
                results: {
                    log: '',
                    classify: '',
                    sql: '',
                    optimize: ''
                },

                async explainLog() {
                    if (!this.logText.trim()) return;
                    this.loading.log = true;
                    this.results.log = '';
                    
                    try {
                        const response = await fetch('/api/ai/explain-log', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({ text: this.logText })
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.results.log = this.formatResponse(data.data.explanation);
                        } else {
                            this.results.log = `<div class="text-red-300">Error: ${data.error}</div>`;
                        }
                    } catch (error) {
                        console.error('API Error:', error);
                        this.results.log = `<div class="text-red-300">Error: ${error.message}</div>`;
                    } finally {
                        this.loading.log = false;
                    }
                },

                async classifyLog() {
                    if (!this.classifyText.trim()) return;
                    this.loading.classify = true;
                    this.results.classify = '';
                    
                    try {
                        const response = await fetch('/api/ai/classify-log', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({ text: this.classifyText })
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.results.classify = this.formatResponse(data.data.classification);
                        } else {
                            this.results.classify = `<div class="text-red-300">Error: ${data.error}</div>`;
                        }
                    } catch (error) {
                        console.error('API Error:', error);
                        this.results.classify = `<div class="text-red-300">Error: ${error.message}</div>`;
                    } finally {
                        this.loading.classify = false;
                    }
                },

                async explainSql() {
                    if (!this.sqlQuery.trim()) return;
                    this.loading.sql = true;
                    this.results.sql = '';
                    
                    try {
                        const response = await fetch('/api/ai/explain-sql', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({ query: this.sqlQuery })
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.results.sql = this.formatResponse(data.data.explanation);
                        } else {
                            this.results.sql = `<div class="text-red-300">Error: ${data.error}</div>`;
                        }
                    } catch (error) {
                        console.error('API Error:', error);
                        this.results.sql = `<div class="text-red-300">Error: ${error.message}</div>`;
                    } finally {
                        this.loading.sql = false;
                    }
                },

                async optimizeSql() {
                    if (!this.optimizeQuery.trim()) return;
                    this.loading.optimize = true;
                    this.results.optimize = '';
                    
                    try {
                        const response = await fetch('/api/ai/optimize-sql', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({ query: this.optimizeQuery })
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.results.optimize = this.formatResponse(data.data.optimization);
                        } else {
                            this.results.optimize = `<div class="text-red-300">Error: ${data.error}</div>`;
                        }
                    } catch (error) {
                        console.error('API Error:', error);
                        this.results.optimize = `<div class="text-red-300">Error: ${error.message}</div>`;
                    } finally {
                        this.loading.optimize = false;
                    }
                },

                formatResponse(text) {
                    return text
                        .replace(/\n\n/g, '</p><p>')
                        .replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/```(.*?)```/g, '<code class="bg-black/20 px-2 py-1 rounded">$1</code>')
                        .replace(/`(.*?)`/g, '<code class="bg-black/20 px-1 py-0.5 rounded text-xs">$1</code>');
                },

                loadExample(type) {
                    const examples = {
                        log1: '[2024-08-01 11:25:33] production.ERROR: Undefined variable $user in /app/Http/Controllers/UserController.php:45',
                        log2: '[2024-08-01 11:25:33] production.WARNING: Database connection timeout after 30 seconds',
                        log3: '[2024-08-01 11:25:33] production.INFO: User login successful for user@example.com',
                        sql1: 'SELECT u.name, u.email, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.created_at >= "2024-01-01" GROUP BY u.id, u.name, u.email HAVING order_count > 0 ORDER BY order_count DESC',
                        sql2: 'SELECT * FROM users WHERE email = "user@example.com"',
                        sql3: 'SELECT p.name, p.price, c.name as category FROM products p INNER JOIN categories c ON p.category_id = c.id WHERE p.active = 1 ORDER BY p.created_at DESC'
                    };
                    
                    if (examples[type]) {
                        if (type.startsWith('log')) {
                            this.logText = examples[type];
                            this.classifyText = examples[type];
                        } else if (type.startsWith('sql')) {
                            this.sqlQuery = examples[type];
                            this.optimizeQuery = examples[type];
                        }
                    }
                }
            }
        }
    </script>
</body>
</html> 