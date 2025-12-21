<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use Illuminate\Http\Request;

class LeaseController extends Controller
{
    // Helper to check user permissions for a module
    private function checkPermission($user, $action)
    {
        $hasPermission = $user->roles()
            ->whereHas('modules.permissions', function ($q) use ($action) {
                $q->where('action', $action)
                  ->where('modules.code', 'LEASE');
            })->exists();

        if (!$hasPermission) {
            abort(403, "Unauthorized: {$action} permission required for LEASE.");
        }
    }

    // List all leases
    public function index(Request $request)
    {
        $this->checkPermission($request->user(), 'view');

        return response()->json(
            Lease::with('building')->get(),
            200
        );
    }

    // Create a lease
    public function store(Request $request)
    {
        $this->checkPermission($request->user(), 'create');

        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'client_lease_id' => 'nullable|string',
            'system_lease_id' => 'nullable|string',
            'tenant_legal_name' => 'nullable|string',
            'landlord_legal_name' => 'nullable|string',
            'legacy_entity_name' => 'nullable|string',
            'deed_of_grant' => 'nullable|string',
            'within_landlord_tenant_act' => 'nullable|string',
            'lease_clauses' => 'nullable|string',
            'lease_acts' => 'nullable|string',
            'lease_penalties' => 'nullable|string',
            'lease_hierarchy' => 'nullable|string',
            'lease_agreement_date' => 'nullable|string',
            'possession_date' => 'nullable|string',
            'rent_commencement_date' => 'nullable|string',
            'current_commencement_date' => 'nullable|string',
            'termination_date' => 'nullable|string',
            'current_term' => 'nullable|string',
            'current_term_remaining' => 'nullable|string',
            'lease_status' => 'nullable|string',
            'lease_possible_expiration' => 'nullable|string',
            'lease_type' => 'nullable|string',
            'lease_recovery_type' => 'nullable|string',
            'lease_rentable_area' => 'nullable|string',
            'measure_units' => 'nullable|string',
            'primary_use' => 'nullable|string',
            'additional_use' => 'nullable|string',
            'account_type' => 'nullable|string',
            'escalation_type' => 'nullable|string',
            'security_deposit_type' => 'nullable|string',
            'security_deposit_amount' => 'nullable|string',
            'security_deposit_deposited_date' => 'nullable|string',
            'security_deposit_return_date' => 'nullable|string',
            'portfolio' => 'nullable|string',
            'portfolio_sub_group' => 'nullable|string',
            'lease_version' => 'nullable|string',
            'parent_lease_id' => 'nullable|string',
            'critical_lease' => 'nullable|string',
            'compliance_status' => 'nullable|string',
            'lease_source' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $lease = Lease::create($validated);

        return response()->json([
            'message' => 'Lease created successfully',
            'data' => $lease
        ], 201);
    }

    // Show a specific lease
    public function show(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'view');

        $lease = Lease::with('building')->find($id);

        if (!$lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        return response()->json($lease, 200);
    }

    // Update a lease
    public function update(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'edit');

        $lease = Lease::find($id);

        if (!$lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        $lease->update($request->all());

        return response()->json([
            'message' => 'Lease updated successfully',
            'data' => $lease
        ], 200);
    }

    // Delete a lease (mark as inactive)
    public function destroy(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'delete');

        $lease = Lease::find($id);

        if (!$lease) {
            return response()->json(['message' => 'Lease not found'], 404);
        }

        $lease->update(['lease_status' => 'Inactive']);

        return response()->json(['message' => 'Lease marked as inactive'], 200);
    }
}
