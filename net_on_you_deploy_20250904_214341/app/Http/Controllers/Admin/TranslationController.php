<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Display a listing of translations
     */
    public function index(Request $request)
    {
        $query = Translation::with(['language', 'createdByAdmin', 'updatedByAdmin']);

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language_code', $request->language);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Search by key or value
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        $translations = $query->orderBy('language_code')
            ->orderBy('key')
            ->paginate(50);

        // Get filter options
        $languages = Language::active()->orderBy('name')->get();
        $modules = Translation::getAvailableModules();

        return view('admin.translations.index', compact('translations', 'languages', 'modules'));
    }

    /**
     * Show the form for creating a new translation
     */
    public function create()
    {
        $languages = Language::active()->orderBy('name')->get();
        $modules = Translation::getAvailableModules();
        
        return view('admin.translations.create', compact('languages', 'modules'));
    }

    /**
     * Store a newly created translation
     */
    public function store(Request $request)
    {
        $request->validate([
            'language_code' => 'required|exists:languages,code',
            'key' => 'required|string|max:191',
            'value' => 'required|string',
            'module' => 'nullable|string|max:50',
        ]);

        // Check if translation already exists
        $existing = Translation::where('language_code', $request->language_code)
            ->where('key', $request->key)
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['key' => 'Translation key already exists for this language.']);
        }

        $translation = Translation::create([
            'language_code' => $request->language_code,
            'key' => $request->key,
            'value' => $request->value,
            'module' => $request->module,
            'created_by_admin_id' => auth()->id(),
            'updated_by_admin_id' => auth()->id(),
        ]);

        // Clear cache
        $this->clearTranslationCache($request->language_code);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation created successfully.');
    }

    /**
     * Show the form for editing the specified translation
     */
    public function edit(Translation $translation)
    {
        $languages = Language::active()->orderBy('name')->get();
        $modules = Translation::getAvailableModules();
        
        return view('admin.translations.edit', compact('translation', 'languages', 'modules'));
    }

    /**
     * Update the specified translation
     */
    public function update(Request $request, Translation $translation)
    {
        $request->validate([
            'language_code' => 'required|exists:languages,code',
            'key' => 'required|string|max:191',
            'value' => 'required|string',
            'module' => 'nullable|string|max:50',
        ]);

        // Check if key already exists for this language (excluding current translation)
        $existing = Translation::where('language_code', $request->language_code)
            ->where('key', $request->key)
            ->where('id', '!=', $translation->id)
            ->first();

        if ($existing) {
            return back()->withInput()->withErrors(['key' => 'Translation key already exists for this language.']);
        }

        $translation->update([
            'language_code' => $request->language_code,
            'key' => $request->key,
            'value' => $request->value,
            'module' => $request->module,
            'updated_by_admin_id' => auth()->id(),
        ]);

        // Clear cache
        $this->clearTranslationCache($request->language_code);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation updated successfully.');
    }

    /**
     * Remove the specified translation
     */
    public function destroy(Translation $translation)
    {
        $languageCode = $translation->language_code;
        $translation->delete();

        // Clear cache
        $this->clearTranslationCache($languageCode);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Bulk import translations
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'language_code' => 'required|exists:languages,code',
            'translations' => 'required|array',
            'translations.*.key' => 'required|string|max:191',
            'translations.*.value' => 'required|string',
            'translations.*.module' => 'nullable|string|max:50',
        ]);

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($request->translations as $index => $translationData) {
            try {
                $translation = Translation::updateOrCreate(
                    [
                        'language_code' => $request->language_code,
                        'key' => $translationData['key'],
                    ],
                    [
                        'value' => $translationData['value'],
                        'module' => $translationData['module'] ?? null,
                        'updated_by_admin_id' => auth()->id(),
                    ]
                );

                if ($translation->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        // Clear cache
        $this->clearTranslationCache($request->language_code);

        $message = "Import completed. Imported: {$imported}, Updated: {$updated}";
        if (!empty($errors)) {
            $message .= ". Errors: " . count($errors);
        }

        return back()->with('success', $message)->withErrors($errors);
    }

    /**
     * Export translations
     */
    public function export(Request $request)
    {
        $request->validate([
            'language_code' => 'required|exists:languages,code',
            'format' => 'required|in:csv,json',
        ]);

        $translations = Translation::where('language_code', $request->language_code)
            ->orderBy('module')
            ->orderBy('key')
            ->get();

        if ($request->format === 'csv') {
            return $this->exportCsv($translations, $request->language_code);
        } else {
            return $this->exportJson($translations, $request->language_code);
        }
    }

    /**
     * Export as CSV
     */
    private function exportCsv($translations, $languageCode)
    {
        $filename = "translations_{$languageCode}_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($translations) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Key', 'Value', 'Module']);
            
            // Add data
            foreach ($translations as $translation) {
                fputcsv($file, [
                    $translation->key,
                    $translation->value,
                    $translation->module,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export as JSON
     */
    private function exportJson($translations, $languageCode)
    {
        $filename = "translations_{$languageCode}_" . date('Y-m-d_H-i-s') . ".json";
        
        $data = $translations->map(function ($translation) {
            return [
                'key' => $translation->key,
                'value' => $translation->value,
                'module' => $translation->module,
            ];
        });

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Clear translation cache for specific language
     */
    private function clearTranslationCache(string $languageCode): void
    {
        Cache::flush();
    }
}
