<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use App\Models\WorkflowLog;
use App\Models\User;

class LeaseController extends Controller
{
    // Helper to check user permissions for a module
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
            $q->where('code', 'LEASE');
        })
        ->exists();

    if (!$hasPermission) {
        abort(403, "Unauthorized: {$action} permission required for LEASE");
    }
}

    // List all leases
 public function index(Request $request)
{
    $this->checkPermission($request->user(), 'view');

    $perPage = $request->get('per_page', 10);
    $search = $request->get('search');

    $query = Lease::with('building');

    // 🔍 Apply search (customize fields if needed)
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('client_lease_id', 'like', "%$search%")
              ->orWhere('tenant_legal_name', 'like', "%$search%")
              ->orWhere('landlord_legal_name', 'like', "%$search%");
        });
    }

    $leases = $query->paginate($perPage);

    return response()->json($leases, 200);
}

    // Create a lease
    public function store(Request $request)
    {
        $this->checkPermission($request->user(), 'create');

        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
        'lease_administrator_id'=>'nullable|exists:users,user_id',
        'permitted_use' => 'nullable|string',
        'has_break_option' => 'nullable|string',
        'break_option_date' => 'nullable|string',
        'break_notice_period' => 'nullable|string',
        'next_rent_review_date' => 'nullable|string',
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
                 WorkflowService::log([
        'lease_id' => $lease->id,
        'status' => 'CREATED',
        'notes' => 'Lease created',
        'stage_order' => 1
    ]);

        return response()->json([
            'message' => 'Lease created successfully',
            'data' => $lease
        ], 201);
    }

    // Show a specific lease
public function show(Request $request, $id)
{
    $this->checkPermission($request->user(), 'view');

    $lease = Lease::with(['building', 'leaseAdministrator:user_id,username'])->find($id);
    if (!$lease) {
        return response()->json(['message' => 'Lease not found'], 404);
    }

    /* 🔹 Fetch workflow logs for this lease */
    $logs = WorkflowLog::where('lease_id', $id)
        ->orderBy('created_at', 'asc')
        ->get();

    /* 🔹 Identify creator & approver */
    $createdLog  = $logs->firstWhere('status', 'CREATED');
    $approvedLog = $logs->firstWhere('status', 'APPROVED');

    $creator  = null;
    $approver = null;

    if ($createdLog) {
        $user = User::find($createdLog->user_id);
        if ($user) {
            $creator = [
                'user_id'    => $user->id,
                'name'       => trim($user->user_firstName . ' ' . $user->user_lastName),
                'created_at' => $createdLog->created_at
            ];
        }
    }

    if ($approvedLog) {
        $user = User::find($approvedLog->user_id);
        if ($user) {
            $approver = [
                'user_id'     => $user->id,
                'name'        => trim($user->user_firstName . ' ' . $user->user_lastName),
                'approved_at' => $approvedLog->created_at
            ];
        }
    }

    return response()->json([
        'lease' => $lease,
        'lease_administrator' => $lease->leaseAdministrator
        ? [
            'user_id' => $lease->leaseAdministrator->user_id,
            'username' => $lease->leaseAdministrator->username
        ]
        : null,
        'workflow' => [
            'status' => $approvedLog ? 'APPROVED' : 'PENDING',
            'created_by' => $creator,
            'approved_by' => $approver ?? 'Approval Pending'
        ]
    ], 200);
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

    WorkflowService::log([
        'lease_id' => $lease->id,
        'status' => 'UPDATED',
        'notes' => 'Lease updated',
        'stage_order' => 2
    ]);

    // 🔹 Reload lease with relationships
    $lease->load([
        'building',
        'leaseAdministrator:user_id,username'
    ]);

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
        WorkflowService::log([
    'lease_id' => $lease->id,
    'status' => 'DELETED',
    'notes' => 'Lease deleted',
        'stage_order' => 3
]);

        return response()->json(['message' => 'Lease marked as inactive'], 200);
    }

    private function exportCSV($data, $columns)
{
    $filename = "leases.csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($data, $columns) {
        $file = fopen('php://output', 'w');

        fputcsv($file, $columns);

        foreach ($data as $row) {
            fputcsv($file, $row->toArray());
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

private function exportPDF($data, $columns)
{
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.leases', [
        'data' => $data,
        'columns' => $columns
    ]);

    return $pdf->download('leases.pdf');
}

public function export(Request $request)
{
    $this->checkPermission($request->user(), 'view');

    $columns = $request->input('columns', []);
    $format = $request->input('format', 'csv');
    $search = $request->input('search');

    $query = Lease::query();

    // 🔍 Search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('client_lease_id', 'like', "%$search%")
              ->orWhere('tenant_legal_name', 'like', "%$search%")
              ->orWhere('landlord_legal_name', 'like', "%$search%");
        });
    }

    $data = $query->get();

    // Filter columns
    $filtered = $data->map(function ($item) use ($columns) {
        return collect($item)->only($columns);
    });

    switch ($format) {
        case 'csv':
            return $this->exportCSV($filtered, $columns);

        case 'json':
            return response()->json($filtered);

        case 'pdf':
            return $this->exportPDF($filtered, $columns);

        default:
            return response()->json(['error' => 'Invalid format'], 400);
    }
}

public function columns(Request $request)
{
    $this->checkPermission($request->user(), 'view');

    $columns = \Schema::getColumnListing('leases');

    return response()->json($columns);
}

}
