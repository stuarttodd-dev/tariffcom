#!/bin/bash
set -e

echo "🧪 Running Complete Test Pipeline..."
echo "=================================="

REQUIRED_NODE_MAJOR=18

if [ -s "$HOME/.nvm/nvm.sh" ]; then
    export NVM_DIR="$HOME/.nvm"
    . "$HOME/.nvm/nvm.sh"
fi

if command -v nvm &> /dev/null; then
    echo "🔄 Using latest Node.js version via nvm..."
    nvm install node &> /dev/null
    nvm use node
elif command -v fnm &> /dev/null; then
    echo "🔄 Using latest Node.js version via fnm..."
    fnm install latest &> /dev/null
    fnm use latest
elif command -v asdf &> /dev/null; then
    echo "🔄 Using latest Node.js version via asdf..."
    asdf install nodejs latest &> /dev/null
    asdf global nodejs latest
elif command -v n &> /dev/null; then
    echo "🔄 Using latest Node.js version via n..."
    sudo n latest
else
    echo "⚠️  Could not auto-switch Node.js version. Please ensure Node.js 18+ is installed."
fi

node -v

NODE_MAJOR=$(node -v | sed 's/v\([0-9]*\).*/\1/')
if [ "$NODE_MAJOR" -lt "$REQUIRED_NODE_MAJOR" ]; then
    echo "❌ Node.js 18+ is required. Current version: $(node -v)"
    exit 1
fi

if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel project root."
    exit 1
fi

if command -v docker &> /dev/null; then
    if ! docker info &> /dev/null; then
        echo "⚠️  Warning: Docker is not running. Some tests may fail."
    fi
fi

echo ""
echo "📝 Running PHP Tests (Pest)..."
echo "-------------------------------"
if command -v ./vendor/bin/pest &> /dev/null; then
    ./vendor/bin/pest
    echo "✅ PHP Tests passed!"
else
    echo "❌ Pest not found. Run 'composer install' first."
    exit 1
fi

echo ""
echo "⚡ Running JavaScript Tests (Jest)..."
echo "------------------------------------"
if command -v npm &> /dev/null; then
    npm test
    echo "✅ JavaScript Tests passed!"
else
    echo "❌ npm not found. Please install Node.js."
    exit 1
fi

echo ""
echo "🔎 Running PHP Standards Check (PHPStan, PHPMD, PHPCS, Rector)..."
echo "--------------------------------------------------------------"
if command -v composer &> /dev/null; then
    composer standards:check
    echo "✅ Standards check passed!"
else
    echo "❌ composer not found. Please install Composer."
    exit 1
fi

echo ""
echo "🌐 Running End-to-End Tests (Playwright)..."
echo "------------------------------------------"

# Build frontend before E2E tests
if command -v npm &> /dev/null; then
    echo "🛠️  Building frontend (npm run build)..."
    npm run build
    echo "✅ Frontend build complete!"
else
    echo "❌ npm not found. Please install Node.js."
    exit 1
fi

# Check if Laravel app is running
if curl -s http://localhost:8000 > /dev/null 2>&1; then
    echo "✅ Laravel app is running on http://localhost:8000"
else
    echo "⚠️  Warning: Laravel app is not running on http://localhost:8000"
    echo "   Starting Laravel app in background..."
    php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
    SERVER_PID=$!
    echo "   Laravel app started with PID: $SERVER_PID"
    echo "   Waiting 5 seconds for app to be ready..."
    sleep 5
fi

if command -v npx &> /dev/null; then
    npx playwright test
    echo "✅ End-to-End Tests passed!"
else
    echo "❌ npx not found. Please install Node.js."
    exit 1
fi

# Cleanup background server if we started it
if [ ! -z "$SERVER_PID" ]; then
    echo "   Stopping background Laravel app..."
    kill $SERVER_PID 2>/dev/null || true
fi

echo ""
echo "All tests passed!"
echo "==================="
echo "Summary:"
echo "   - PHP Tests (Pest): ✅"
echo "   - JavaScript Tests (Jest): ✅"
echo "   - End-to-End Tests (Playwright): ✅"
echo ""
echo "Your application is ready for production!"