<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\MagazineView;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MagazineController extends Controller
{
    /**
     * Display a listing of available magazines for subscribers
     */
    public function index(Request $request)
    {
        // Check if user has active subscription
        if (!Auth::check() || !$this->hasActiveSubscription()) {
            return view('magazines.subscription-required');
        }

        $query = Magazine::with('admin')
            ->active()
            ->published();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
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
            ->paginate(12);

        // Get available categories and languages for filters
        $categories = Magazine::getAvailableCategories();
        $languages = Magazine::getAvailableLanguages();

        return view('magazines.index', compact('magazines', 'categories', 'languages'));
    }

    /**
     * Show magazine details
     */
    public function show(Magazine $magazine)
    {
        // Check if user has active subscription
        if (!Auth::check() || !$this->hasActiveSubscription()) {
            return view('magazines.subscription-required');
        }

        // Check if magazine is accessible
        if (!$magazine->isActive() || !$magazine->isPublished()) {
            abort(404, 'Magazine not found');
        }

        // Record view for analytics
        $magazine->recordView(Auth::id(), 'viewed');

        return view('magazines.show', compact('magazine'));
    }

    /**
     * Download magazine file
     */
    public function download(Magazine $magazine)
    {
        // Check if user has active subscription
        if (!Auth::check() || !$this->hasActiveSubscription()) {
            abort(403, 'Active subscription required');
        }

        // Check if magazine is accessible
        if (!$magazine->isActive() || !$magazine->isPublished()) {
            abort(404, 'Magazine not found');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($magazine->file_path)) {
            abort(404, 'File not found');
        }

        // Record download for analytics
        $magazine->recordView(Auth::id(), 'downloaded');

        return Storage::disk('public')->download(
            $magazine->file_path,
            $magazine->file_name
        );
    }

    /**
     * Get magazine categories for API
     */
    public function categories()
    {
        $categories = Magazine::getAvailableCategories();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Get magazine languages for API
     */
    public function languages()
    {
        $languages = Magazine::getAvailableLanguages();
        
        return response()->json([
            'success' => true,
            'languages' => $languages
        ]);
    }

    /**
     * Check if user has active subscription
     */
    private function hasActiveSubscription(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->exists();
    }

    /**
     * Get user's magazine access status
     */
    public function accessStatus()
    {
        if (!Auth::check()) {
            return response()->json([
                'has_access' => false,
                'message' => 'Please log in to access magazines'
            ]);
        }

        $hasAccess = $this->hasActiveSubscription();
        
        if ($hasAccess) {
            $subscription = Subscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->first();

            return response()->json([
                'has_access' => true,
                'subscription' => [
                    'expires_at' => $subscription->end_date->format('Y-m-d H:i:s'),
                    'days_remaining' => now()->diffInDays($subscription->end_date, false)
                ]
            ]);
        }

        return response()->json([
            'has_access' => false,
            'message' => 'Active subscription required to access magazines'
        ]);
    }
}
