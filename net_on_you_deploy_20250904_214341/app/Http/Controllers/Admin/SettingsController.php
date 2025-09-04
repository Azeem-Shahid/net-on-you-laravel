<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SensitiveChangesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show settings index page
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('settings.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $settings = Setting::orderBy('key')->get();
        $groupedSettings = Setting::getGroupedSettings();

        return view('admin.settings.index', compact('settings', 'groupedSettings'));
    }

    /**
     * Update a setting
     */
    public function update(Request $request, $key)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('settings.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:1000',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $setting = Setting::where('key', $key)->first();
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $oldValue = $setting->value;
        $newValue = $request->input('value');

        // Update setting
        $setting->update([
            'value' => $newValue,
            'updated_by_admin_id' => auth('admin')->id(),
        ]);

        // Clear cache
        Cache::forget("setting_{$key}");

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'update_setting',
            $key,
            $oldValue,
            $newValue,
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'update_setting',
            'setting',
            $key,
            [
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'setting' => $setting->fresh()
        ]);
    }

    /**
     * Update multiple settings
     */
    public function updateMultiple(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('settings.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required|string|max:1000',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $adminId = auth('admin')->id();
        $settings = $request->input('settings');
        $reason = $request->input('reason');

        foreach ($settings as $settingData) {
            $setting = Setting::where('key', $settingData['key'])->first();
            if ($setting) {
                $oldValue = $setting->value;
                $newValue = $settingData['value'];

                // Update setting
                $setting->update([
                    'value' => $newValue,
                    'updated_by_admin_id' => $adminId,
                ]);

                // Clear cache
                Cache::forget("setting_{$settingData['key']}");

                // Log sensitive change
                SensitiveChangesLog::log(
                    $adminId,
                    'update_setting',
                    $settingData['key'],
                    $oldValue,
                    $newValue,
                    $request->ip()
                );
            }
        }

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            $adminId,
            'update_multiple_settings',
            'settings',
            null,
            [
                'count' => count($settings),
                'reason' => $reason,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Get setting value
     */
    public function getValue($key)
    {
        $value = Setting::getValue($key);
        return response()->json(['value' => $value]);
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('settings.manage')) {
            abort(403, 'Unauthorized action.');
        }

        Setting::clearCache();

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'clear_settings_cache',
            'settings',
            null,
            ['action' => 'cache_cleared']
        );

        return response()->json([
            'success' => true,
            'message' => 'Settings cache cleared successfully'
        ]);
    }
}

