<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    /**
     * Display a listing of languages
     */
    public function index()
    {
        $languages = Language::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new language
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created language
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $language = Language::create([
            'code' => strtolower($request->code),
            'name' => $request->name,
            'status' => $request->status,
            'is_default' => false,
        ]);

        // Clear cache
        Cache::forget('available_languages');

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language created successfully.');
    }

    /**
     * Show the form for editing the specified language
     */
    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * Update the specified language
     */
    public function update(Request $request, Language $language)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $language->update([
            'code' => strtolower($request->code),
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Clear cache
        Cache::forget('available_languages');

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated successfully.');
    }

    /**
     * Remove the specified language
     */
    public function destroy(Language $language)
    {
        // Prevent deletion of default language
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete the default language.');
        }

        // Check if language has translations
        if ($language->translations()->exists()) {
            return back()->with('error', 'Cannot delete language with existing translations.');
        }

        $language->delete();

        // Clear cache
        Cache::forget('available_languages');

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language deleted successfully.');
    }

    /**
     * Set language as default
     */
    public function setDefault(Language $language)
    {
        if (!$language->isActive()) {
            return back()->with('error', 'Cannot set inactive language as default.');
        }

        $language->setAsDefault();

        // Clear cache
        Cache::forget('available_languages');

        return back()->with('success', 'Default language updated successfully.');
    }

    /**
     * Toggle language status
     */
    public function toggleStatus(Language $language)
    {
        // Prevent deactivating default language
        if ($language->is_default) {
            return back()->with('error', 'Cannot deactivate the default language.');
        }

        $language->update([
            'status' => $language->status === 'active' ? 'inactive' : 'active'
        ]);

        // Clear cache
        Cache::forget('available_languages');

        return back()->with('success', 'Language status updated successfully.');
    }
}
