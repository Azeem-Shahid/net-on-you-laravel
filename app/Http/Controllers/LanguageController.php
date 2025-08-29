<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Switch language
     */
    public function switch(Request $request)
    {
        $request->validate([
            'language' => 'required|string|max:10',
        ]);

        $languageCode = $request->language;
        
        // Set the language
        $this->translationService->setLanguage($languageCode);

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
        $currentLanguage = $this->translationService->getCurrentLanguage();
        $availableLanguages = $this->translationService->getAvailableLanguages();
        
        return response()->json([
            'current' => $currentLanguage,
            'available' => $availableLanguages,
        ]);
    }
}
