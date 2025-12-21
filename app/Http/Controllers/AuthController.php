<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login user and return token + RBAC data
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email_id' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email_id', $credentials['email_id'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        /**
         * Build permissions structure:
         * BUILDING => [create, view, edit]
         * LEASE    => [view, approve]
         */
        $permissions = [];

        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {

                // Get module via pivot.module_id
                $module = Module::find($permission->pivot->module_id);

                if (!$module) continue;

                $permissions[$module->code][] = $permission->action;
            }
        }

        // Remove duplicates
        foreach ($permissions as $module => $actions) {
            $permissions[$module] = array_values(array_unique($actions));
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email_id' => $user->email_id,
                'roles' => $user->roles->pluck('code'),
            ],
            'permissions' => $permissions
        ], 200);
    }

     /**
     * Register a new user (Admin only)
     */
    public function register(Request $request)
    {
        // Only Admin can register new users
        $authUser = $request->user();
        if (!$authUser || !$authUser->roles()->where('code', 'ADMIN')->exists()) {
            return response()->json([
                'message' => 'Unauthorized: Admin role required.'
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'username'        => 'required|string|max:255|unique:users,username',
            'user_firstName'  => 'required|string|max:255',
            'user_lastName'   => 'required|string|max:255',
            'email_id'        => 'required|string|email|max:255|unique:users,email_id',
            'password'        => 'required|string|min:8',
            'roles'           => 'required|array',       // array of role codes
            'roles.*'         => 'string|exists:roles,code',
        ]);

        // Create user
        $user = User::create([
            'username'       => $validated['username'],
            'user_firstName' => $validated['user_firstName'],
            'user_lastName'  => $validated['user_lastName'],
            'email_id'       => $validated['email_id'],
            'password'       => Hash::make($validated['password']),
            'user_type'      => $validated['roles.*'] ?? 'default',
        ]);

        // Assign roles
        $roleIds = Role::whereIn('code', $validated['roles'])->pluck('id');
        $user->roles()->sync($roleIds);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user->load('roles')
        ], 201);
    }

}
