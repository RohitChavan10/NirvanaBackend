<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use App\Models\WorkflowLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
   /**
     * Check if user has permission for BUILDING module
     */
private function checkPermission($user, string $action)
{
    if (!$user) {
        abort(401, 'Unauthenticated');
    }

    $hasPermission = $user->roles()
        ->whereHas('permissions', function ($q) use ($action) {
            $q->where('action', $action);
        })
        ->whereHas('modules', function ($q) {
            $q->where('code', 'BUILDING');
        })
        ->exists();

    if (!$hasPermission) {
        abort(403, "Unauthorized: {$action} permission required for BUILDING");
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
         WorkflowService::log([
        'building_id' => $building->id,
        'status' => 'CREATED',
        'notes' => 'Building created',
        'stage_order' => 1
    ]);

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

    /* 🔹 Fetch workflow logs for this building */
    $logs = WorkflowLog::where('building_id', $id)
        ->orderBy('created_at', 'asc')
        ->get();

    /* 🔹 Identify creator */
    $createdLog = $logs->firstWhere('status', 'CREATED');
    $approvedLog = $logs->firstWhere('status', 'APPROVED');

    $creator = null;
    $approver = null;

    if ($createdLog) {
        $user = User::find($createdLog->user_id);
        if ($user) {
            $creator = [
                'user_id' => $user->user_id,
                'name' => trim($user->user_firstName . ' ' . $user->user_lastName),
                'created_at' => $createdLog->created_at
            ];
        }
    }

    if ($approvedLog) {
        $user = User::find($approvedLog->user_id);
        if ($user) {
            $approver = [
                'user_id' => $user->user_id,
                'name' => trim($user->user_firstName  . ' ' . $user->user_lastName),
                'approved_at' => $approvedLog->created_at
            ];
        }
    }

    return response()->json([
        'building' => $building,
        'workflow' => [
            'status' => $approvedLog ? 'APPROVED' : 'PENDING',
            'created_by' => $creator,
            'approved_by' => $approver ?? 'Approval Pending'
        ]
    ], 200);
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
         WorkflowService::log([
        'building_id' => $building->id,
        'status' => 'UPDATED',
        'notes' => 'Building updated',
        'stage_order' => 2
    ]);
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
         WorkflowService::log([
        'building_id' => $building->id,
        'status' => 'DELETED',
        'notes' => 'Building deleted',
        'stage_order' => 3
    ]);
        return response()->json(['message' => 'Building marked as inactive'], 200);
    }
}
