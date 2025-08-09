#!/bin/bash

echo "🤖 Laravel AI Log Analyzer - Ollama Setup Script"
echo "================================================"
echo ""

# Check if Ollama is already installed
if command -v ollama &> /dev/null; then
    echo "✅ Ollama is already installed!"
    echo "Current version: $(ollama --version)"
else
    echo "📦 Installing Ollama..."
    
    # Detect OS and install Ollama
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        echo "Installing on macOS..."
        curl -fsSL https://ollama.ai/install.sh | sh
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        # Linux
        echo "Installing on Linux..."
        curl -fsSL https://ollama.ai/install.sh | sh
    else
        echo "❌ Unsupported operating system: $OSTYPE"
        echo "Please install Ollama manually from https://ollama.ai"
        exit 1
    fi
fi

echo ""
echo "🔄 Starting Ollama service..."
ollama serve &
OLLAMA_PID=$!

# Wait for Ollama to start
echo "⏳ Waiting for Ollama to start..."
sleep 5

# Check if Ollama is running
if curl -s http://localhost:11434/api/tags > /dev/null; then
    echo "✅ Ollama is running successfully!"
else
    echo "❌ Failed to start Ollama. Please check the installation."
    exit 1
fi

echo ""
echo "📥 Pulling recommended model (llama2)..."
ollama pull llama2

echo ""
echo "🧪 Testing Ollama connection..."
if curl -s http://localhost:11434/api/tags | grep -q "llama2"; then
    echo "✅ Ollama is ready with llama2 model!"
else
    echo "❌ Failed to verify llama2 model. Please check the installation."
    exit 1
fi

echo ""
echo "🔧 Setting up environment..."
if [ ! -f .env ]; then
    if [ -f env.example ]; then
        cp env.example .env
        echo "✅ Created .env file from env.example"
    else
        echo "❌ env.example not found. Please create .env file manually."
    fi
else
    echo "✅ .env file already exists"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Edit .env file and set:"
echo "   OLLAMA_BASE_URL=http://localhost:11434"
echo "   OLLAMA_MODEL=llama2"
echo ""
echo "2. Install Laravel dependencies:"
echo "   composer install"
echo ""
echo "3. Generate application key:"
echo "   php artisan key:generate"
echo ""
echo "4. Start the Laravel server:"
echo "   php artisan serve"
echo ""
echo "5. Test the API:"
echo "   php test_ollama_api.php"
echo ""
echo "🌐 Visit http://localhost:8000 to see the API documentation"
echo ""

# Keep Ollama running in background
echo "🔄 Ollama is running in the background (PID: $OLLAMA_PID)"
echo "To stop Ollama, run: kill $OLLAMA_PID" 