<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{

    /**
     * Switch language
     */
    public function switch(Request $request)
    {
        $request->validate([
            'language' => 'required|string|max:10',
        ]);

        $languageCode = $request->language;
        
        // Save to session for guests
        session(['language' => $languageCode]);
        
        // Save to user language field if logged in
        if (auth()->check()) {
            auth()->user()->update(['language' => $languageCode]);
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Language changed successfully.',
                'language' => $languageCode
            ]);
        }

        // Redirect back or to intended URL for regular requests
        $redirectUrl = $request->get('redirect', url()->previous());
        
        return redirect($redirectUrl)->with('success', 'Language changed successfully.');
    }

    /**
     * Get current language info
     */
    public function current()
    {
        // Get current language from user preference or session
        $currentLanguage = 'en'; // Default
        
        if (auth()->check()) {
            $user = auth()->user();
            if (isset($user->language) && $user->language) {
                $currentLanguage = $user->language;
            }
        } elseif (session()->has('language')) {
            $currentLanguage = session('language');
        }
        
        // Return available languages for GTranslate widget
        $availableLanguages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'es', 'name' => 'Español']
        ];
        
        return response()->json([
            'current' => $currentLanguage,
            'available' => $availableLanguages,
        ]);
    }
}
