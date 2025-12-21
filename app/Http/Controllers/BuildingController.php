<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    // Helper to check user permissions for a module
    private function checkPermission($user, $action)
    {
        $hasPermission = $user->roles()
            ->whereHas('modules.permissions', function ($q) use ($action) {
                $q->where('action', $action)
                  ->where('modules.code', 'BUILDING');
            })->exists();

        if (!$hasPermission) {
            abort(403, "Unauthorized: {$action} permission required for BUILDING.");
        }
    }

    // List all buildings
    public function index(Request $request)
    {
        $this->checkPermission($request->user(), 'view');

        return response()->json(Building::all(), 200);
    }

    // Store a new building
    public function store(Request $request)
    {
        $this->checkPermission($request->user(), 'create');

        $validated = $request->validate([
            'system_building_id' => 'nullable|string|unique:buildings',
            'sio' => 'nullable|string',
            'building_name' => 'required|string',
            'address_1' => 'nullable|string',
            'city' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'country' => 'nullable|string',
            'clli' => 'nullable|string',
            'building_type' => 'nullable|string',
            'building_rentable_area' => 'nullable|string',
            'building_measure_units' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'geocode_latitude' => 'nullable|string',
            'geocode_longitude' => 'nullable|string',
            'building_images' => 'nullable|string',
            'building_status' => 'nullable|string',
            'purchase_price' => 'nullable|string',
            'currency_type' => 'nullable|string',
            'construction_year' => 'nullable|string',
            'last_renovation_year' => 'nullable|string',
            'portfolio' => 'nullable|string',
            'portfolio_sub_group' => 'nullable|string',
            'ownership_type' => 'nullable|string',
            'managed_by' => 'nullable|string',
        ]);

        $building = Building::create($validated);

        return response()->json($building, 201);
    }

    // Show a specific building
    public function show(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'view');

        $building = Building::find($id);
        if (!$building) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($building, 200);
    }

    // Update building
    public function update(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'edit');

        $building = Building::find($id);
        if (!$building) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $building->update($request->all());
        return response()->json($building, 200);
    }

    // Delete building (mark as inactive)
    public function destroy(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'delete');

        $building = Building::find($id);
        if (!$building) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $building->update(['building_status' => 'Inactive']);
        return response()->json(['message' => 'Building marked as inactive'], 200);
    }
}
