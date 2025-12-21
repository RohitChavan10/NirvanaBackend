<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;

class ModuleController extends Controller
{
    // List all modules
    public function index()
    {
        $modules = Module::all();
        return response()->json($modules);
    }

    // Create a new module
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:modules,name',
            'code' => 'required|string|unique:modules,code',
        ]);

        $module = Module::create($validated);

        return response()->json([
            'message' => 'Module created successfully',
            'module' => $module
        ], 201);
    }

    // Update a module
    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:modules,name,' . $module->id,
            'code' => 'sometimes|required|string|unique:modules,code,' . $module->id,
        ]);

        $module->update($validated);

        return response()->json([
            'message' => 'Module updated successfully',
            'module' => $module
        ]);
    }

    // Delete a module
    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();

        return response()->json([
            'message' => 'Module deleted successfully'
        ]);
    }
}
