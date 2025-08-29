#!/bin/bash

echo "🚀 Installing Multi-Language Support for NetOnYou..."
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from your Laravel project root directory"
    exit 1
fi

echo "📦 Running database migrations..."
php artisan migrate

echo "🌱 Running database seeders..."
php artisan db:seed --class=LanguageSeeder

echo "🔄 Regenerating Composer autoload..."
composer dump-autoload

echo "🧹 Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "✅ Multi-Language Support installation completed!"
echo ""
echo "🎯 Next steps:"
echo "1. Access admin panel at /admin/login"
echo "2. Go to Languages section to manage languages"
echo "3. Go to Translations section to manage translations"
echo "4. Use the language switcher on any page"
echo ""
echo "🔧 Helper functions available:"
echo "- t('key') - Get translation"
echo "- t('key', ['param' => 'value']) - With replacements"
echo "- t('key', [], 'module') - With module context"
echo "- current_language() - Get current language"
echo "- available_languages() - Get available languages"
echo ""
echo "📚 For more information, see MULTI_LANGUAGE_MODULE_README.md"
