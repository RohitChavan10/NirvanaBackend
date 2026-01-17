<?php

namespace App\Http\Controllers;

use App\Models\LeaseExpense;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use App\Models\WorkflowLog;
use App\Models\User;    

class LeaseExpenseController extends Controller
{
    // Helper to check user permissions for the EXPENSE module
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
            $q->where('code', 'EXPENSE');
        })
        ->exists();

    if (!$hasPermission) {
        abort(403, "Unauthorized: {$action} permission required for EXPENSE");
    }
}
    // List all expenses
    public function index(Request $request)
    {
        $this->checkPermission($request->user(), 'view');

        return response()->json(LeaseExpense::all(), 200);
    }

    // Create a new expense
    public function store(Request $request)
    {
        $this->checkPermission($request->user(), 'create');

        $validated = $request->validate([
            'lease_id' => 'nullable|exists:leases,id',
            'system_building_id' => 'nullable|exists:buildings,id',
            'expense_year' => 'nullable|string',
            'expense_period' => 'nullable|string',
            'expense_category' => 'nullable|string',
            'expense_type' => 'nullable|string',
            'amount' => 'nullable|string',
            'currency' => 'nullable|string',
            'status' => 'nullable|string',
            'document_url' => 'nullable|string',
            'is_escalable' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $expense = LeaseExpense::create($validated);
        WorkflowService::log([
    'expense_id' => $expense->id,
      'status' => 'CREATED',
        'notes' => 'Expense created',
        'stage_order' => 1
]);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense
        ], 201);
    }

    // Show a specific expense
 public function show(Request $request, $id)
 {
    $this->checkPermission($request->user(), 'view');

    $expense = LeaseExpense::where('expense_id', $id)->first();

    if (!$expense) {
        return response()->json(['message' => 'Expense not found'], 404);
    }

    /* 🔹 Fetch workflow logs for this expense */
    $logs = WorkflowLog::where('expense_id', $id)
        ->orderBy('created_at', 'asc')
        ->get();

    $createdLog  = $logs->firstWhere('status', 'CREATED');
    $approvedLog = $logs->firstWhere('status', 'APPROVED');

    $creator = null;
    $approver = null;

    if ($createdLog) {
        $user = User::find($createdLog->user_id);
        if ($user) {
            $creator = [
                'user_id'    => $user->user_id,
                'name'       => trim($user->user_firstName . ' ' . $user->user_lastName),
                'created_at' => $createdLog->created_at
            ];
        }
    }

    if ($approvedLog) {
        $user = User::find($approvedLog->user_id);
        if ($user) {
            $approver = [
                'user_id'     => $user->user_id,
                'name'        => trim($user->user_firstName . ' ' . $user->user_lastName),
                'approved_at' => $approvedLog->created_at
            ];
        }
    }

    return response()->json([
        'expense' => $expense,
        'workflow' => [
            'status' => $approvedLog ? 'APPROVED' : 'PENDING',
            'created_by' => $creator,
            'approved_by' => $approver ?? 'Approval Pending'
        ]
    ], 200);
 }

    // Update an expense
public function update(Request $request, $id)
{
    $this->checkPermission($request->user(), 'edit');

    $expense = LeaseExpense::where('expense_id', $id)->first();

    if (!$expense) {
        return response()->json(['message' => 'Expense not found'], 404);
    }

    $validated = $request->validate([
        'expense_year' => 'nullable|string',
        'expense_period' => 'nullable|string',
        'expense_category' => 'nullable|string',
        'expense_type' => 'nullable|string',
        'amount' => 'nullable|string',
        'currency' => 'nullable|string',
        'note' => 'nullable|string',
    ]);

    $expense->update($validated);

    WorkflowService::log([
        'expense_id' => $expense->expense_id,
        'status' => 'UPDATED',
        'notes' => 'Expense updated',
        'stage_order' => 2
    ]);

    return response()->json([
        'message' => 'Expense updated successfully',
        'data' => $expense
    ]);
}


    // Delete an expense (mark as inactive)
    public function destroy(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'delete');

        $expense = LeaseExpense::where('expense_id', $id)->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->update(['status' => 'Inactive']);
        WorkflowService::log([
    'expense_id' => $expense->id,
    'status' => 'DELETED',
     'notes' => 'Expense updated',
        'stage_order' => 3
]);

        return response()->json(['message' => 'Expense marked as inactive'], 200);
    }
}

