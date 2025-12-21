<?php

namespace App\Http\Controllers;

use App\Models\LeaseExpense;
use Illuminate\Http\Request;

class LeaseExpenseController extends Controller
{
    // Helper to check user permissions for the EXPENSE module
    private function checkPermission($user, $action)
    {
        $hasPermission = $user->roles()
            ->whereHas('modules.permissions', function ($q) use ($action) {
                $q->where('action', $action)
                  ->where('modules.code', 'EXPENSE');
            })->exists();

        if (!$hasPermission) {
            abort(403, "Unauthorized: {$action} permission required for EXPENSE.");
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

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense
        ], 201);
    }

    // Show a specific expense
    public function show(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'view');

        $expense = LeaseExpense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense, 200);
    }

    // Update an expense
    public function update(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'edit');

        $expense = LeaseExpense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->update($request->all());

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense
        ], 200);
    }

    // Delete an expense (mark as inactive)
    public function destroy(Request $request, $id)
    {
        $this->checkPermission($request->user(), 'delete');

        $expense = LeaseExpense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $expense->update(['status' => 'Inactive']);

        return response()->json(['message' => 'Expense marked as inactive'], 200);
    }
}
