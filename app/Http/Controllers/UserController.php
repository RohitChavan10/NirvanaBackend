<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List all users
public function index(Request $request)
{
    $perPage = $request->get('per_page', 10);
    $search = $request->get('search');

    $query = User::with('roles');

    if ($search) {
        $query->where('username', 'like', '%' . $search . '%');
    }

    $users = $query->paginate($perPage);

    return response()->json($users, 200);
}

    // Assign roles to a user
    public function assignRoles(Request $request, $id)
    {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->roles()->sync($request->role_ids); // attach roles

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => $user->load('roles')
        ], 200);
    }
}
