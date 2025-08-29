<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\Admin;
use App\Models\SensitiveChangesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show roles index page
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('roles.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = AdminRole::with('createdByAdmin')->orderBy('created_at', 'desc')->get();
        $availablePermissions = AdminRole::getAvailablePermissions();
        $admins = Admin::where('status', 'active')->get();

        return view('admin.roles.index', compact('roles', 'availablePermissions', 'admins'));
    }

    /**
     * Store a new role
     */
    public function store(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('roles.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:100|unique:admin_roles,role_name',
            'permissions' => 'required|array',
            'permissions.*' => 'boolean',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = AdminRole::create([
            'role_name' => $request->input('role_name'),
            'permissions' => $request->input('permissions'),
            'created_by_admin_id' => auth('admin')->id(),
        ]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'create_admin_role',
            $role->role_name,
            null,
            json_encode($request->input('permissions')),
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'create_admin_role',
            'admin_role',
            $role->id,
            [
                'role_name' => $role->role_name,
                'permissions' => $request->input('permissions'),
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'role' => $role->load('createdByAdmin')
        ]);
    }

    /**
     * Update a role
     */
    public function update(Request $request, AdminRole $role)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('roles.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:100|unique:admin_roles,role_name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'boolean',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldPermissions = $role->permissions;
        $newPermissions = $request->input('permissions');

        // Update role
        $role->update([
            'role_name' => $request->input('role_name'),
            'permissions' => $newPermissions,
        ]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'update_admin_role',
            $role->role_name,
            json_encode($oldPermissions),
            json_encode($newPermissions),
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'update_admin_role',
            'admin_role',
            $role->id,
            [
                'role_name' => $role->role_name,
                'old_permissions' => $oldPermissions,
                'new_permissions' => $newPermissions,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'role' => $role->fresh()->load('createdByAdmin')
        ]);
    }

    /**
     * Delete a role
     */
    public function destroy(Request $request, AdminRole $role)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('roles.manage')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if role is in use
        $adminsUsingRole = Admin::where('role', $role->role_name)->count();
        if ($adminsUsingRole > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role that is currently assigned to admins'
            ], 422);
        }

        $roleName = $role->role_name;
        $permissions = $role->permissions;

        $role->delete();

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'delete_admin_role',
            $roleName,
            json_encode($permissions),
            null,
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'delete_admin_role',
            'admin_role',
            $role->id,
            [
                'role_name' => $roleName,
                'permissions' => $permissions,
                'reason' => $request->input('reason', 'No reason provided'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Get role details
     */
    public function show(AdminRole $role)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('roles.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $role->load('createdByAdmin');
        $availablePermissions = AdminRole::getAvailablePermissions();

        return response()->json([
            'role' => $role,
            'availablePermissions' => $availablePermissions
        ]);
    }

    /**
     * Get available permissions
     */
    public function getPermissions()
    {
        $permissions = AdminRole::getAvailablePermissions();
        return response()->json(['permissions' => $permissions]);
    }
}

