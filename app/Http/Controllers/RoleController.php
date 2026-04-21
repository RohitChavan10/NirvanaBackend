<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // List all roles
  public function index()
{
    $roles = Role::all();
    $modules = Module::all();
    $permissions = Permission::all();

    $data = $roles->map(function ($role) use ($modules) {
        return [
            'id' => $role->id,
            'code' => $role->code,
            'modules' => $modules->map(function ($module) use ($role) {

                $perms = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module_id', $module->id)
                    ->pluck('permission_id')
                    ->toArray();

                return [
                    'id' => $module->id,
                    'code' => $module->code,
                    'permissions' => Permission::whereIn('id', $perms)->get()
                ];
            })
        ];
    });

    return response()->json($data);
}

    // Create a new role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'code' => 'required|string|unique:roles,code',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    // Update a role
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:roles,name,' . $role->id,
            'code' => 'sometimes|required|string|unique:roles,code,' . $role->id,
        ]);

        $role->update($validated);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    // Delete a role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    // Assign role to a user
    public function assignRoleToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->roles()->syncWithoutDetaching([$validated['role_id']]);

        return response()->json([
            'message' => 'Role assigned to user successfully'
        ]);
    }

    // Revoke role from a user
    public function revokeRoleFromUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->roles()->detach($validated['role_id']);

        return response()->json([
            'message' => 'Role revoked from user successfully'
        ]);
    }

    // Assign permissions to a role for a specific module
public function assignPermissions(Request $request)
{
    $validated = $request->validate([
        'role_id' => 'required|exists:roles,id',
        'module_id' => 'required|exists:modules,id',
        'permission_ids' => 'required|array',
    ]);

    DB::table('role_module_permissions')
        ->where('role_id', $validated['role_id'])
        ->where('module_id', $validated['module_id'])
        ->delete();

    foreach ($validated['permission_ids'] as $pid) {
        DB::table('role_module_permissions')->insert([
            'role_id' => $validated['role_id'],
            'module_id' => $validated['module_id'],
            'permission_id' => $pid,
        ]);
    }

    return response()->json(['message' => 'Permissions updated']);
}
}
