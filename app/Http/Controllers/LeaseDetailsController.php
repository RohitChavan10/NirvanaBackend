<?php

namespace App\Http\Controllers;

use App\Models\LeaseDetail;
use Illuminate\Http\Request;

class LeaseDetailsController extends Controller
{
    // List all leases
    public function index()
    {
        return response()->json(LeaseDetail::with('building')->get(), 200);
    }

    // Create a lease
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:building_details,id',
            'lease_contract' => 'required|string',
            'clauses' => 'nullable|string',
            'contact_details' => 'nullable|string',
            'history' => 'nullable|string',
        ]);

        $lease = LeaseDetail::create($validated);

        return response()->json([
            'message' => 'Lease created successfully',
            'data' => $lease
        ], 201);
    }

    // Show lease
    public function show($id)
    {
        $lease = LeaseDetail::with('building')->find($id);

        if (! $lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        return response()->json($lease, 200);
    }

    // Update lease
    public function update(Request $request, $id)
    {
        $lease = LeaseDetail::find($id);

        if (! $lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        $lease->update($request->all());

        return response()->json([
            'message' => 'Lease updated successfully',
            'data'    => $lease
        ], 200);
    }

    // Delete lease
    public function destroy($id)
    {
        $lease = LeaseDetail::find($id);

        if (! $lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        $lease->delete();

        return response()->json(['message' => 'Lease deleted successfully'], 200);
    }
}
