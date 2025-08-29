<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Update user's language preference
     */
    public function updateLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language code',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = Auth::user();
        $language = $request->input('language');

        // Update user's language preference
        $user->update([
            'language' => $language
        ]);

        // Set session locale
        session(['locale' => $language]);
        app()->setLocale($language);

        return response()->json([
            'success' => true,
            'message' => 'Language preference updated successfully',
            'language' => $language
        ]);
    }

    /**
     * Get user's current language preference
     */
    public function getLanguage()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'language' => $user->language ?? 'en'
        ]);
    }
}

