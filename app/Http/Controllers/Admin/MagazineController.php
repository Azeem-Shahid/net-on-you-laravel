<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\MagazineVersion;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MagazineController extends Controller
{
    /**
     * Display a listing of magazines
     */
    public function index(Request $request)
    {
        $query = Magazine::with('admin');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by language
        if ($request->filled('language_code')) {
            $query->where('language_code', $request->language_code);
        }

        $magazines = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get available categories and languages for filters
        $categories = Magazine::getAvailableCategories();
        $languages = Magazine::getAvailableLanguages();

        // Log magazine listing access
        AdminActivityLog::log(
            auth()->id(),
            'view_magazines',
            'magazine_list',
            null,
            ['filters' => $request->all()]
        );

        return view('admin.magazines.index', compact('magazines', 'categories', 'languages'));
    }

    /**
     * Show magazine creation form
     */
    public function create()
    {
        $categories = Magazine::getAvailableCategories();
        $languages = Magazine::getAvailableLanguages();
        
        return view('admin.magazines.create', compact('categories', 'languages'));
    }

    /**
     * Store a new magazine
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'magazine_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'category' => 'nullable|string|max:100',
            'language_code' => 'required|string|max:10',
            'status' => 'required|in:active,inactive',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('magazine_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('magazines', $fileName, 'public');

            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $coverImageName = time() . '_cover_' . $coverImage->getClientOriginalName();
                $coverImagePath = $coverImage->storeAs('magazines/covers', $coverImageName, 'public');
            }

            $magazine = Magazine::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'cover_image_path' => $coverImagePath,
                'category' => $request->category,
                'language_code' => $request->language_code,
                'status' => $request->status,
                'uploaded_by_admin_id' => auth()->id(),
                'published_at' => $request->published_at,
            ]);

            // Log magazine creation
            AdminActivityLog::log(
                auth()->id(),
                'create_magazine',
                'magazine',
                $magazine->id,
                [
                    'title' => $magazine->title,
                    'file_size' => $magazine->file_size,
                    'file_name' => $magazine->file_name,
                    'category' => $magazine->category,
                    'language_code' => $magazine->language_code
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Magazine uploaded successfully',
                'magazine' => $magazine
            ]);

        } catch (\Exception $e) {
            // Clean up files if magazine creation fails
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if (isset($coverImagePath) && Storage::disk('public')->exists($coverImagePath)) {
                Storage::disk('public')->delete($coverImagePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload magazine: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show magazine details
     */
    public function show(Magazine $magazine)
    {
        $magazine->load(['admin', 'entitlements.user', 'versions.admin', 'views.user']);

        // Log magazine view
        AdminActivityLog::log(
            auth()->id(),
            'view_magazine',
            'magazine',
            $magazine->id,
            ['title' => $magazine->title]
        );

        return view('admin.magazines.show', compact('magazine'));
    }

    /**
     * Show magazine edit form
     */
    public function edit(Magazine $magazine)
    {
        $categories = Magazine::getAvailableCategories();
        $languages = Magazine::getAvailableLanguages();
        
        return view('admin.magazines.edit', compact('magazine', 'categories', 'languages'));
    }

    /**
     * Update magazine
     */
    public function update(Request $request, Magazine $magazine)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'nullable|string|max:100',
            'language_code' => 'required|string|max:10',
            'status' => 'required|in:active,inactive,archived',
            'published_at' => 'nullable|date',
        ]);

        $oldStatus = $magazine->status;
        $oldCoverImage = $magazine->cover_image_path;

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '_cover_' . $coverImage->getClientOriginalName();
            $coverImagePath = $coverImage->storeAs('magazines/covers', $coverImageName, 'public');
            $validated['cover_image_path'] = $coverImagePath;

            // Delete old cover image
            if ($oldCoverImage && Storage::disk('public')->exists($oldCoverImage)) {
                Storage::disk('public')->delete($oldCoverImage);
            }
        }

        $magazine->update($validated);

        // Log magazine update
        AdminActivityLog::log(
            auth()->id(),
            'update_magazine',
            'magazine',
            $magazine->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'changes' => $validated
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Magazine updated successfully'
        ]);
    }

    /**
     * Upload new version of magazine
     */
    public function uploadVersion(Request $request, Magazine $magazine)
    {
        $request->validate([
            'version_file' => 'required|file|mimes:pdf|max:10240',
            'version' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        try {
            $file = $request->file('version_file');
            $fileName = time() . '_v' . $request->version . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('magazines/versions', $fileName, 'public');

            $version = MagazineVersion::create([
                'magazine_id' => $magazine->id,
                'file_path' => $filePath,
                'version' => $request->version,
                'notes' => $request->notes,
                'uploaded_by_admin_id' => auth()->id(),
            ]);

            // Log version upload
            AdminActivityLog::log(
                auth()->id(),
                'upload_magazine_version',
                'magazine_version',
                $version->id,
                [
                    'magazine_id' => $magazine->id,
                    'magazine_title' => $magazine->title,
                    'version' => $request->version
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Magazine version uploaded successfully',
                'version' => $version
            ]);

        } catch (\Exception $e) {
            // Clean up file if version creation fails
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload version: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete magazine
     */
    public function destroy(Magazine $magazine)
    {
        try {
            // Delete main file from storage
            if (Storage::disk('public')->exists($magazine->file_path)) {
                Storage::disk('public')->delete($magazine->file_path);
            }

            // Delete cover image
            if ($magazine->cover_image_path && Storage::disk('public')->exists($magazine->cover_image_path)) {
                Storage::disk('public')->delete($magazine->cover_image_path);
            }

            // Delete version files
            foreach ($magazine->versions as $version) {
                if (Storage::disk('public')->exists($version->file_path)) {
                    Storage::disk('public')->delete($version->file_path);
                }
            }

            $title = $magazine->title;
            $magazine->delete();

            // Log magazine deletion
            AdminActivityLog::log(
                auth()->id(),
                'delete_magazine',
                'magazine',
                $magazine->id,
                ['title' => $title, 'file_path' => $magazine->file_path]
            );

            return response()->json([
                'success' => true,
                'message' => 'Magazine deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete magazine: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download magazine file
     */
    public function download(Magazine $magazine)
    {
        if (!Storage::disk('public')->exists($magazine->file_path)) {
            abort(404, 'File not found');
        }

        // Log download
        AdminActivityLog::log(
            auth()->id(),
            'download_magazine',
            'magazine',
            $magazine->id,
            ['title' => $magazine->title]
        );

        return Storage::disk('public')->download(
            $magazine->file_path,
            $magazine->file_name
        );
    }

    /**
     * Toggle magazine status
     */
    public function toggleStatus(Magazine $magazine)
    {
        $newStatus = $magazine->status === 'active' ? 'inactive' : 'active';
        $oldStatus = $magazine->status;
        
        $magazine->update(['status' => $newStatus]);

        // Log status change
        AdminActivityLog::log(
            auth()->id(),
            'toggle_magazine_status',
            'magazine',
            $magazine->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'title' => $magazine->title
            ]
        );

        $message = $newStatus === 'active' ? 'Magazine activated successfully' : 'Magazine deactivated successfully';
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'new_status' => $newStatus
        ]);
    }

    /**
     * Bulk update magazine status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'magazine_ids' => 'required|array',
            'magazine_ids.*' => 'exists:magazines,id',
            'status' => 'required|in:active,inactive,archived'
        ]);

        $magazines = Magazine::whereIn('id', $request->magazine_ids)->get();
        $count = 0;

        foreach ($magazines as $magazine) {
            $oldStatus = $magazine->status;
            $magazine->update(['status' => $request->status]);
            $count++;

            // Log each status change
            AdminActivityLog::log(
                auth()->id(),
                'bulk_update_magazine_status',
                'magazine',
                $magazine->id,
                [
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'title' => $magazine->title
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Updated {$count} magazines successfully"
        ]);
    }
}
