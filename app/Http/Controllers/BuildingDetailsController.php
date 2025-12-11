<?php

namespace App\Http\Controllers;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingDetailsController extends Controller
{
      // Get all buildings
    public function index()
    {
        return response()->json(Building::all(), 200);
    }

    // Create a building
    public function store(Request $request)
    {
        $validated = $request->validate([
            'uid' => 'required|string|unique:building_details',
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'managed_by' => 'required|string',
            'building_age' => 'nullable|string',
            'status' => 'required|string',
            'area' => 'nullable|string',
            'nearest_landmarks' => 'nullable|string',
            'rent' => 'nullable|numeric',
            'contact_person' => 'nullable|string',
            'history' => 'nullable|string',
            'images' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $building = Building::create($validated);

        return response()->json([
            'message' => 'Building created successfully',
            'data' => $building
        ], 201);
    }

    // Show single building
    public function show($id)
    {
        $building = Building::find($id);

        if (! $building) {
            return response()->json(['message' => 'Building not found'], 404);
        }

        return response()->json($building, 200);
    }

    // Update building
    public function update(Request $request, $id)
    {
        $building = Building::find($id);

        if (! $building) {
            return response()->json(['message' => 'Building not found'], 404);
        }

        $building->update($request->all());

        return response()->json([
            'message' => 'Building updated successfully',
            'data'    => $building
        ], 200);
    }

    // Delete building
    public function destroy($id)
    {
        $building = Building::find($id);

        if (! $building) {
            return response()->json(['message' => 'Building not found'], 404);
        }

        $building->delete();

        return response()->json(['message' => 'Building deleted successfully'], 200);
    }
}
