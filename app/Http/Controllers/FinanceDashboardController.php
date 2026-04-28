<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Building;
use App\Models\LeaseExpense;
use App\Models\Certificate;
use App\Models\AccountInvoice;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
    public function stats()
    {
        /*
        |--------------------------------------------------------------------------
        | KPI CARDS (same pattern as your UI)
        |--------------------------------------------------------------------------
        */
        $cards = [
            "total_expenses" => LeaseExpense::sum('amount'),

            "lease_expenses" => LeaseExpense::whereNotNull('lease_id')->sum('amount'),

            "building_expenses" => LeaseExpense::whereNotNull('building_id')->sum('amount'),

            "overdue_invoices" => AccountInvoice::whereDate('due_date', '<', now())->count(),

            "expiring_leases" => Lease::whereDate('termination_date', '<=', now()->addDays(60))->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | PIE: Expense by Category
        |--------------------------------------------------------------------------
        */
        $expenseByCategory = LeaseExpense::select(
            'expense_category',
            DB::raw('SUM(amount) as total')
        )
            ->groupBy('expense_category')
            ->pluck('total', 'expense_category');

        /*
        |--------------------------------------------------------------------------
        | LINE: Monthly Expense Trend
        |--------------------------------------------------------------------------
        */
        $monthlyExpenses = LeaseExpense::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as total')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        /*
        |--------------------------------------------------------------------------
        | TABLE: Top 5 Expiring Leases
        |--------------------------------------------------------------------------
        */
        $expiringLeases = Lease::with('building:id,building_name')
            ->whereNotNull('termination_date')
            ->orderBy('termination_date')
            ->limit(5)
            ->get()
            ->map(function ($lease) {
                return [
                    "tenant" => $lease->tenant_legal_name,
                    "building" => optional($lease->building)->building_name,
                    "expiry" => $lease->termination_date,
                    "status" => $lease->lease_status,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | ALERT: Buildings with Certificate Issues
        |--------------------------------------------------------------------------
        */
        $certificateAlerts = Building::whereDoesntHave('certificates')
            ->orWhereHas('certificates', function ($q) {
                $q->whereDate('expiry_date', '<', now());
            })
            ->get()
            ->map(function ($b) {
                return [
                    "name" => $b->building_name,
                    "city" => $b->city,
                    "country" => $b->country,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | ALERT: Overdue Invoices (Top 5)
        |--------------------------------------------------------------------------
        */
        $overdueInvoices = AccountInvoice::whereDate('due_date', '<', now())
            ->limit(5)
            ->get()
            ->map(function ($inv) {
                return [
                    "invoice" => $inv->invoice_number,
                    "amount" => $inv->total_amount,
                    "due_date" => $inv->due_date,
                ];
            });

        return response()->json([
            "cards" => $cards,

            // Charts (same format as your current dashboard)
            "expense_category_pie" => $expenseByCategory,
            "expenses_over_time" => $monthlyExpenses,

            // Tables / sections
            "expiring_leases_table" => $expiringLeases,
            "certificate_alerts" => $certificateAlerts,
            "overdue_invoices_table" => $overdueInvoices,
        ]);
    }
}