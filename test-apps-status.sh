#!/bin/bash

echo "========================================="
echo "Geraye Healthcare Apps Status Check"
echo "========================================="
echo ""

# Check Laravel Web App
echo "🌐 LARAVEL WEB APP:"
echo "-------------------"
if ps aux | grep -q "[p]hp artisan serve"; then
    echo "✅ Status: RUNNING"
    echo "📍 URL: http://127.0.0.1:8000"
    echo ""
    echo "Testing health endpoint..."
    HEALTH=$(curl -s http://127.0.0.1:8000/api/v1/system/health 2>/dev/null)
    if [ ! -z "$HEALTH" ]; then
        echo "✅ Health Check: PASSED"
        echo "$HEALTH" | head -5
    else
        echo "⚠️  Health Check: No response (may need API configuration)"
    fi
    echo ""
    echo "Testing main page..."
    STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000 2>/dev/null)
    if [ "$STATUS" = "200" ]; then
        echo "✅ Main Page: Accessible (HTTP $STATUS)"
    else
        echo "⚠️  Main Page: HTTP $STATUS"
    fi
else
    echo "❌ Status: NOT RUNNING"
    echo "Start with: cd /Users/yonassayfu/VSProject/gerayehealthcare && php artisan serve"
fi

echo ""
echo "========================================="
echo ""

# Check Flutter Mobile App
echo "📱 FLUTTER MOBILE APP:"
echo "----------------------"
if ps aux | grep -q "[f]lutter"; then
    echo "✅ Status: BUILDING/RUNNING"
    echo "📍 Target: Android Emulator (emulator-5554)"
else
    echo "⏸️  Status: NOT RUNNING"
    echo "Start with: cd gerayehealthcare-mobile-app && flutter run -d emulator-5554"
fi

echo ""

# Check Android Emulator
if adb devices | grep -q "emulator-5554"; then
    echo "✅ Android Emulator: ONLINE"
    adb devices | grep emulator-5554
else
    echo "⚠️  Android Emulator: OFFLINE"
    echo "Start emulator first"
fi

echo ""
echo "========================================="
echo ""
echo "📂 DOCUMENTATION:"
echo "-----------------"
echo "• Deployment Guide: /MD/DEPLOYMENT_STATUS.md"
echo "• Production Readiness: /MD/PRODUCTION_READINESS.md"
echo "• Publishing Guide: /MD/publishMobile.md"
echo "• Master Docs: /MD/GERAYE-ORGANIZED.md"
echo ""
echo "========================================="
