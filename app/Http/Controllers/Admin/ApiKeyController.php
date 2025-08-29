<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\SensitiveChangesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show API keys index page
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('api_keys.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $apiKeys = ApiKey::with('createdByAdmin')
            ->orderBy('created_at', 'desc')
            ->get();

        $availableScopes = $this->getAvailableScopes();

        return view('admin.api-keys.index', compact('apiKeys', 'availableScopes'));
    }

    /**
     * Store a new API key
     */
    public function store(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('api_keys.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'scopes' => 'required|array',
            'scopes.*' => 'boolean',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $apiKey = ApiKey::create([
            'name' => $request->input('name'),
            'scopes' => $request->input('scopes'),
            'is_active' => true,
            'created_by_admin_id' => auth('admin')->id(),
            'metadata' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => $request->input('description', ''),
            ],
        ]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'create_api_key',
            $apiKey->name,
            null,
            json_encode($request->input('scopes')),
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'create_api_key',
            'api_key',
            $apiKey->id,
            [
                'name' => $apiKey->name,
                'scopes' => $request->input('scopes'),
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'API key created successfully',
            'apiKey' => $apiKey->load('createdByAdmin'),
            'fullKey' => $apiKey->key, // Show full key only once
        ]);
    }

    /**
     * Update an API key
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('api_keys.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'scopes' => 'required|array',
            'scopes.*' => 'boolean',
            'is_active' => 'boolean',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldScopes = $apiKey->scopes;
        $oldStatus = $apiKey->is_active;

        // Update API key
        $apiKey->update([
            'name' => $request->input('name'),
            'scopes' => $request->input('scopes'),
            'is_active' => $request->input('is_active', $apiKey->is_active),
        ]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'update_api_key',
            $apiKey->name,
            json_encode($oldScopes),
            json_encode($request->input('scopes')),
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'update_api_key',
            'api_key',
            $apiKey->id,
            [
                'name' => $apiKey->name,
                'old_scopes' => $oldScopes,
                'new_scopes' => $request->input('scopes'),
                'old_status' => $oldStatus,
                'new_status' => $apiKey->is_active,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'API key updated successfully',
            'apiKey' => $apiKey->fresh()->load('createdByAdmin')
        ]);
    }

    /**
     * Delete an API key
     */
    public function destroy(Request $request, ApiKey $apiKey)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('api_keys.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $apiKeyName = $apiKey->name;
        $scopes = $apiKey->scopes;

        $apiKey->delete();

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'delete_api_key',
            $apiKeyName,
            json_encode($scopes),
            null,
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'delete_api_key',
            'api_key',
            $apiKey->id,
            [
                'name' => $apiKeyName,
                'scopes' => $scopes,
                'reason' => $request->input('reason', 'No reason provided'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'API key deleted successfully'
        ]);
    }

    /**
     * Toggle API key status
     */
    public function toggleStatus(Request $request, ApiKey $apiKey)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('api_keys.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $oldStatus = $apiKey->is_active;
        $newStatus = !$oldStatus;

        $apiKey->update(['is_active' => $newStatus]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'toggle_api_key_status',
            $apiKey->name,
            $oldStatus ? 'true' : 'false',
            $newStatus ? 'true' : 'false',
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'toggle_api_key_status',
            'api_key',
            $apiKey->id,
            [
                'name' => $apiKey->name,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $request->input('reason', 'Status toggled'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'API key status updated successfully',
            'is_active' => $newStatus
        ]);
    }

    /**
     * Regenerate API key
     */
    public function regenerate(Request $request, ApiKey $apiKey)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('api_keys.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $oldKey = $apiKey->key;
        $newKey = ApiKey::generateKey();

        $apiKey->update(['key' => $newKey]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'regenerate_api_key',
            $apiKey->name,
            substr($oldKey, 0, 8) . '...',
            substr($newKey, 0, 8) . '...',
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'regenerate_api_key',
            'api_key',
            $apiKey->id,
            [
                'name' => $apiKey->name,
                'reason' => $request->input('reason', 'Key regenerated'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully',
            'newKey' => $newKey
        ]);
    }

    /**
     * Get available scopes
     */
    private function getAvailableScopes(): array
    {
        return [
            'users.read' => 'Read user data',
            'users.write' => 'Create/update user data',
            'magazines.read' => 'Read magazine data',
            'magazines.write' => 'Create/update magazine data',
            'transactions.read' => 'Read transaction data',
            'transactions.write' => 'Create/update transaction data',
            'analytics.read' => 'Read analytics data',
            'reports.generate' => 'Generate reports',
            'webhooks.send' => 'Send webhook notifications',
        ];
    }
}

