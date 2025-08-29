# Custom Language Widget Setup Guide

This guide explains how to set up the custom language widget in your NetOnYou application.

## Overview

The application has been configured to work with a custom language widget for automatic language translation. When users change the language using the custom widget, their preference is automatically saved to their user profile and will be restored when they log in again.

## Setup Steps

### 1. Custom Language Widget

The custom language widget is already implemented and includes:
- English and Spanish language options
- Green button design matching your theme
- Flag icons for each language
- Dropdown menu with smooth animations

### 2. Replace Placeholder in Header Components

#### For Main App Layout (`resources/views/components/header.blade.php`)

The custom language widget is already included and working. No changes needed.

#### For Admin Layout (`resources/views/components/admin-header.blade.php`)

The custom language widget is already included and working. No changes needed.

### 3. Customize Widget Styling

The custom language widget is already styled to match your application's theme with:
- Green background (`bg-action`) matching your color scheme
- Dark text (`text-primary`) for good contrast
- Hover effects and smooth transitions
- Responsive design for mobile devices

### 4. Test the Integration

1. Log in as a user
2. Change the language using the custom language widget (green button)
3. Check the browser console for language change logs
4. Verify that the language preference is saved to the user's profile
5. Log out and log back in to confirm the language preference is restored

## How It Works

### Automatic Language Detection

The `LanguageHandler` JavaScript class automatically:
- Detects when the custom language widget changes the language
- Saves the language preference to the user's profile via API
- Restores the language preference when the user logs in

### User Language Persistence

- Each user's language preference is stored in the `users.language` field
- Admin users also have language preferences stored in the `admins.language` field
- The `SetUserLanguage` middleware automatically sets the application locale based on user preferences

### API Endpoints

- `POST /user/language` - Save user language preference
- `GET /user/language` - Get user's current language preference

## Supported Languages

The system supports all languages that GTranslate provides. Common languages include:
- English (en)
- Spanish (es)
- French (fr)
- German (de)
- Italian (it)
- Portuguese (pt)
- And many more...

## Troubleshooting

### Widget Not Loading

1. Check if the custom language widget component is properly included
2. Verify that Alpine.js is loaded (required for the dropdown functionality)
3. Check browser console for JavaScript errors

### Language Not Saving

1. Ensure user is logged in
2. Check browser console for API errors
3. Verify CSRF token is present
4. Check if the `users.language` field exists in your database

### Language Not Restoring

1. Check if the `SetUserLanguage` middleware is registered
2. Verify the middleware is in the `web` middleware group
3. Check if user has a language preference saved

## Database Requirements

Make sure your database has the following fields:

### Users Table
```sql
ALTER TABLE users ADD COLUMN language VARCHAR(5) DEFAULT 'en';
```

### Admins Table
```sql
ALTER TABLE admins ADD COLUMN language VARCHAR(5) DEFAULT 'en';
```

## Security Notes

- Language preferences are only saved for authenticated users
- CSRF protection is enabled for language update requests
- Language codes are validated before saving

## Performance Considerations

- Language preferences are cached in the session
- The middleware runs on every request but is lightweight
- GTranslate handles the actual translation caching

## Support

If you encounter issues:
1. Check the browser console for error messages
2. Verify your GTranslate account status
3. Check the Laravel logs for backend errors
4. Ensure all required database fields exist
