#!/bin/bash

# Script to check and install compatible Larastan version
# Usage: ./check-larastan-compatibility.sh

echo "🔍 Checking Laravel and Larastan compatibility..."

# Check if composer.json exists
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json not found in current directory"
    exit 1
fi

# Extract Laravel version from composer.json
LARAVEL_VERSION=$(php -r "
\$composer = json_decode(file_get_contents('composer.json'), true);
\$illuminate = \$composer['require']['illuminate/support'] ?? '';
echo \$illuminate;
")

if [ -z "$LARAVEL_VERSION" ]; then
    echo "❌ Could not determine Laravel version from composer.json"
    exit 1
fi

echo "📦 Found illuminate/support constraint: $LARAVEL_VERSION"

# Determine appropriate Larastan version
if [[ $LARAVEL_VERSION == *"12."* ]]; then
    LARASTAN_VERSION="^3.1"
    echo "✅ Laravel 12 detected - recommending Larastan $LARASTAN_VERSION"
elif [[ $LARAVEL_VERSION == *"11."* ]] || [[ $LARAVEL_VERSION == *"10."* ]] || [[ $LARAVEL_VERSION == *"9."* ]] || [[ $LARAVEL_VERSION == *"8."* ]]; then
    LARASTAN_VERSION="^2.0"
    echo "✅ Laravel 8-11 detected - recommending Larastan $LARASTAN_VERSION"
else
    echo "⚠️  Could not determine appropriate Larastan version for Laravel constraint: $LARAVEL_VERSION"
    echo "   Please check manually at: https://github.com/larastan/larastan#compatibility"
    exit 1
fi

echo ""
echo "🚀 To install the compatible version, run:"
echo "   composer require --dev \"larastan/larastan:$LARASTAN_VERSION\""
echo ""
echo "📝 Then run static analysis with:"
echo "   composer phpstan"
