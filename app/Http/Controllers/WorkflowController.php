<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkflowLog;
use App\Services\WorkflowService;

class WorkflowController extends Controller
{
    /**
     * Permission check helper
     */
    private function checkPermission($user, string $action)
    {
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $hasPermission = $user->roles()
            ->whereHas('permissions', fn ($q) => $q->where('action', $action))
            ->whereHas('modules', fn ($q) => $q->where('code', 'WORKFLOW'))
            ->exists();

        if (!$hasPermission) {
            abort(403, "Unauthorized: {$action} permission required for WORKFLOW");
        }
    }

    /**
     * 1️⃣ List all pending workflow requests
     */
public function pending(Request $request)
{
    $this->checkPermission($request->user(), 'view');

    $logs = WorkflowLog::with([
            'user:user_id,user_firstName,user_lastName',
            'building:id,building_name',
            'lease:id,client_lease_id',
            'expense:expense_id,expense_type'
        ])
        ->whereIn('status', ['CREATED', 'UPDATED', 'APPROVED', 'REJECTED'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($log) {

            return [
                'id' => $log->id,
                'status' => $log->status,
                'notes' => $log->notes,
                'created_at' => $log->created_at,

                'user' => $log->user
                    ? $log->user->user_firstName . ' ' . $log->user->user_lastName
                    : null,

                'entity_type' =>
                    $log->building_id ? 'BUILDING' :
                    ($log->lease_id ? 'LEASE' : 'EXPENSE'),

                'entity_name' =>
                    $log->building
                        ? $log->building->building_name
                        : ($log->lease
                            ? $log->lease->client_lease_id
                            : ($log->expense
                                ? 'Expense #' . $log->expense->expense_id
                                : null
                            )
                        )
            ];
        });

    return response()->json($logs);
}

    /**
     * 2️⃣ Show single workflow item (review page)
     */
public function show(Request $request, $id)
{
    $this->checkPermission($request->user(), 'view');

    $log = WorkflowLog::findOrFail($id);

    $entity = null;
    $entityType = null;

    if ($log->building_id) {
        $entityType = 'BUILDING';
        $entity = \App\Models\Building::find($log->building_id);
    } elseif ($log->lease_id) {
        $entityType = 'LEASE';
        $entity = \App\Models\Lease::find($log->lease_id);
    } elseif ($log->expense_id) {
        $entityType = 'EXPENSE';
        $entity = \App\Models\LeaseExpense::find($log->expense_id);
    }

    return response()->json([
        'workflow' => $log,
        'entity_type' => $entityType,
        'entity' => $entity
    ]);
}

    /**
     * 3️⃣ Approve workflow item
     */
    public function approve(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'approve');

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $log = WorkflowLog::findOrFail($id);

        WorkflowService::log([
            'building_id' => $log->building_id,
            'lease_id'    => $log->lease_id,
            'expense_id'  => $log->expense_id,
            'status'      => 'APPROVED',
            'stage_order' => 3,
            'notes'       => $request->notes,
        ]);

        return response()->json(['message' => 'Approved successfully']);
    }

    /**
     * 4️⃣ Reject workflow item
     */
    public function reject(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'approve');

        $request->validate([
            'notes' => 'required|string',
        ]);

        $log = WorkflowLog::findOrFail($id);

        WorkflowService::log([
            'building_id' => $log->building_id,
            'lease_id'    => $log->lease_id,
            'expense_id'  => $log->expense_id,
            'status'      => 'REJECTED',
            'stage_order' => 4,
            'notes'       => $request->notes,
        ]);

        return response()->json(['message' => 'Rejected successfully']);
    }
}
