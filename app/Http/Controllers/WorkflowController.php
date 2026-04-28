<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkflowLog;
use App\Services\WorkflowService;
use App\Models\LeaseExpense;

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

    $perPage = $request->get('per_page', 10);
    $search = $request->get('search');

    $query = WorkflowLog::with([
        'user:user_id,user_firstName,user_lastName',
        'building:id,building_name',
        'lease:id,client_lease_id',
        'expense:expense_id,expense_type'
    ])
    ->whereIn('status', ['CREATED', 'UPDATED', 'APPROVED', 'REJECTED']);

    // 🔍 SEARCH LOGIC
    if ($search) {
        $query->where(function ($q) use ($search) {

            $q->where('status', 'like', "%$search%")
              ->orWhere('notes', 'like', "%$search%")

              ->orWhereHas('building', function ($q2) use ($search) {
                  $q2->where('building_name', 'like', "%$search%");
              })

              ->orWhereHas('lease', function ($q2) use ($search) {
                  $q2->where('client_lease_id', 'like', "%$search%");
              })

              ->orWhereHas('expense', function ($q2) use ($search) {
                  $q2->where('expense_type', 'like', "%$search%");
              })

              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('user_firstName', 'like', "%$search%")
                     ->orWhere('user_lastName', 'like', "%$search%");
              });
        });
    }

    // ✅ PAGINATION (IMPORTANT)
    $logs = $query
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    // ✅ TRANSFORM DATA (same as before)
    $logs->getCollection()->transform(function ($log) {
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
       $entity = \App\Models\LeaseExpense::where('expense_id', $log->expense_id)->first();
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

    // 🔥 Get latest log for entity
    $latestLog = WorkflowLog::where(function ($q) use ($log) {
        if ($log->building_id) {
            $q->where('building_id', $log->building_id);
        } elseif ($log->lease_id) {
            $q->where('lease_id', $log->lease_id);
        } elseif ($log->expense_id) {
            $q->where('expense_id', $log->expense_id);
        }
    })
    ->orderBy('created_at', 'desc')
    ->first();

    // ❌ BLOCK invalid approval
    if (!$latestLog || !in_array($latestLog->status, ['CREATED', 'UPDATED'])) {
        return response()->json([
            'message' => 'Already approved or cannot be approved'
        ], 400);
    }

    // ✅ Update Expense status
    if ($log->expense_id) {
        LeaseExpense::where('expense_id', $log->expense_id)
            ->update(['status' => 'APPROVED']);
    }

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

    // 🔥 Get latest log for entity
    $latestLog = WorkflowLog::where(function ($q) use ($log) {
        if ($log->building_id) {
            $q->where('building_id', $log->building_id);
        } elseif ($log->lease_id) {
            $q->where('lease_id', $log->lease_id);
        } elseif ($log->expense_id) {
            $q->where('expense_id', $log->expense_id);
        }
    })
    ->orderBy('created_at', 'desc')
    ->first();

    // ❌ BLOCK invalid rejection
    if (!$latestLog || !in_array($latestLog->status, ['CREATED', 'UPDATED'])) {
        return response()->json([
            'message' => 'Already processed. Cannot reject.'
        ], 400);
    }

    // ✅ Update Expense status
    if ($log->expense_id) {
        LeaseExpense::where('expense_id', $log->expense_id)
            ->update(['status' => 'REJECTED']);
    }

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
