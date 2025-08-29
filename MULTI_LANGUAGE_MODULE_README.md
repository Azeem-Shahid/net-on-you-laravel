# Module 7: Multi-Language Support (Admin-Manageable)

## Overview

This module provides comprehensive multi-language support for the entire website, allowing administrators to manage languages and translations through an intuitive admin panel. Users can switch between languages, and the system automatically detects and applies their preferences.

## Features

### Core Functionality
- **Language Management**: Add, edit, enable/disable languages
- **Translation Management**: Create and manage translation keys and values
- **Module-based Organization**: Group translations by modules (auth, dashboard, payment, etc.)
- **User Preferences**: Each user can set their preferred language
- **Automatic Detection**: Browser language detection with fallback to default
- **Caching**: Efficient translation caching for performance
- **Mobile-First Design**: Responsive interface that works on all devices

### Admin Controls
- **Language CRUD**: Full language management (create, read, update, delete)
- **Translation CRUD**: Manage all translation keys and values
- **Bulk Operations**: Import/export translations in CSV or JSON format
- **Role-based Access**: Only admins can manage translations
- **Audit Trail**: Track who created/updated translations

### User Experience
- **Language Switcher**: Available on all pages (top-right)
- **Persistent Preferences**: Language choice saved for logged-in users
- **Session Storage**: Guest users' language choice stored in session
- **Fallback System**: Missing translations fallback to default language

## Database Structure

### Tables

#### `languages`
- `id` (bigint, PK, auto)
- `code` (varchar 10, unique index) - Language code (e.g., 'en', 'ur')
- `name` (varchar 50) - Language name (e.g., 'English', 'Urdu')
- `is_default` (tinyint) - 1 = default, 0 = not default
- `status` (enum: active, inactive)
- `created_at`, `updated_at` (timestamps)

#### `translations`
- `id` (bigint, PK, auto)
- `language_code` (varchar 10, index) - References languages.code
- `key` (varchar 191, index) - Translation key (unique per language)
- `value` (text) - Translated string
- `module` (varchar 50, nullable) - Module grouping (auth, dashboard, etc.)
- `created_by_admin_id` (bigint, index) - Admin who created
- `updated_by_admin_id` (bigint, index) - Admin who last updated
- `created_at`, `updated_at` (timestamps)

#### `user_language_preferences`
- `id` (bigint, PK, auto)
- `user_id` (bigint, index) - References users.id
- `language_code` (varchar 10, index) - References languages.code
- `created_at`, `updated_at` (timestamps)

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Run Seeders
```bash
php artisan db:seed --class=LanguageSeeder
```

### 3. Update Composer Autoload
```bash
composer dump-autoload
```

### 4. Add Language Switcher to Layouts
Include the language switcher component in your main layouts:

```blade
<!-- In header/navigation -->
@include('components.language-switcher')
```

## Usage

### For Developers

#### Translation Helper Function
Use the `t()` helper function in your views:

```blade
<h1>{{ t('welcome_message') }}</h1>
<p>{{ t('dashboard_description') }}</p>
```

#### With Module Context
```blade
<button>{{ t('save', [], 'common') }}</button>
<label>{{ t('email', [], 'auth') }}</label>
```

#### With Replacements
```blade
<p>{{ t('welcome_user', ['name' => $user->name], 'dashboard') }}</p>
```

#### In Controllers
```php
use App\Services\TranslationService;

class DashboardController extends Controller
{
    public function index(TranslationService $translationService)
    {
        $welcomeMessage = $translationService->get('welcome_message', ['name' => auth()->user()->name]);
        
        return view('dashboard', compact('welcomeMessage'));
    }
}
```

### For Administrators

#### Managing Languages
1. Go to `/admin/languages`
2. Add new languages with code and name
3. Set default language (only one can be default)
4. Enable/disable languages as needed

#### Managing Translations
1. Go to `/admin/translations`
2. Create new translation keys and values
3. Organize by modules for better management
4. Use bulk import/export for efficiency

#### Best Practices
- Use descriptive, consistent key names
- Group related translations by module
- Test translations in different languages
- Keep default language (English) complete

## API Endpoints

### Public Routes
- `POST /language/switch` - Switch language
- `GET /language/current` - Get current language info

### Admin Routes
- `GET /admin/languages` - List languages
- `POST /admin/languages` - Create language
- `PUT /admin/languages/{id}` - Update language
- `DELETE /admin/languages/{id}` - Delete language
- `POST /admin/languages/{id}/set-default` - Set as default
- `POST /admin/languages/{id}/toggle-status` - Toggle status

- `GET /admin/translations` - List translations
- `POST /admin/translations` - Create translation
- `PUT /admin/translations/{id}` - Update translation
- `DELETE /admin/translations/{id}` - Delete translation
- `POST /admin/translations/bulk-import` - Bulk import
- `GET /admin/translations/export` - Export translations

## Configuration

### Translation Service
The `TranslationService` handles all translation logic:

- **Cache TTL**: 1 hour (configurable)
- **Fallback Logic**: Default language → Key name
- **Browser Detection**: Automatic language detection
- **Session Storage**: Guest user preferences

### Helper Functions
- `t($key, $replacements = [], $module = null)` - Get translation
- `current_language()` - Get current language code
- `available_languages()` - Get available languages

## Testing

### Functional Testing
```bash
php artisan test --filter=MultiLanguageTest
```

### Manual Testing Checklist
- [ ] Add new language (e.g., Urdu)
- [ ] Set as default language
- [ ] Create translations for key modules
- [ ] Switch language on frontend
- [ ] Verify all translated text updates
- [ ] Test fallback to default language
- [ ] Disable language and verify it's hidden
- [ ] Test mobile responsiveness

### Admin Testing
- [ ] Only admins can manage translations
- [ ] Editing updates everywhere immediately
- [ ] Bulk import/export works correctly
- [ ] Role-based access control

### User Experience Testing
- [ ] Logged-in users see saved preferences
- [ ] Guest users fallback to browser/default
- [ ] Mobile dropdown is accessible
- [ ] Language switching is smooth

## Security Features

- **Input Sanitization**: All keys and values are sanitized
- **Role-based Access**: Only admins can manage translations
- **Audit Logging**: Track all admin actions
- **CSRF Protection**: All forms protected
- **Validation**: Server-side validation for all inputs

## Performance Features

- **Caching**: Translations cached for 1 hour
- **Database Indexing**: Optimized queries with proper indexes
- **Lazy Loading**: Translations loaded only when needed
- **Memory Efficient**: Minimal memory footprint

## Mobile-First Design

### Responsive Breakpoints
- **Mobile (360px)**: Language dropdown fits in header, admin tables become cards
- **Tablet/Desktop**: Full table view, filters visible
- **Touch-Friendly**: Large touch targets for mobile users

### Brand Colors
- **Header**: `#1d003f` (Dark Purple)
- **Active**: `#00ff00` (Green)
- **Inactive**: `#ff0000` (Red)

## Troubleshooting

### Common Issues

#### Translations Not Showing
1. Check if language is active
2. Verify translation exists in database
3. Clear translation cache: `php artisan cache:clear`
4. Check helper function is loaded

#### Language Switcher Not Working
1. Verify routes are registered
2. Check CSRF token in forms
3. Ensure TranslationService is working
4. Check browser console for errors

#### Performance Issues
1. Clear translation cache
2. Check database indexes
3. Monitor cache hit rates
4. Optimize translation queries

### Debug Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerate autoload
composer dump-autoload

# Check translation service
php artisan tinker
>>> app(\App\Services\TranslationService::class)->get('test_key')
```

## Future Enhancements

### Planned Features
- **RTL Support**: Right-to-left language support
- **Translation Memory**: AI-powered translation suggestions
- **Version Control**: Translation versioning and rollback
- **API Integration**: External translation services
- **Advanced Caching**: Redis-based caching
- **Translation Analytics**: Usage statistics and insights

### Customization Options
- **Custom Fallbacks**: Language-specific fallback chains
- **Dynamic Loading**: AJAX-based language switching
- **SEO Integration**: Language-specific URLs
- **Content Localization**: Date, number, and currency formatting

## Support

For technical support or questions about the multi-language module:

1. Check this README first
2. Review the code comments
3. Check Laravel logs for errors
4. Test with minimal setup
5. Contact development team

## License

This module is part of the NetOnYou application and follows the same licensing terms.
