<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Lease;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            "cards" => [
                "buildings" => Building::count(),
                "leases" => Lease::count(),
                "active_leases" => Lease::where('lease_status', 'Active')->count(),
                "expired_leases" => Lease::whereDate('termination_date', '<', now())->count(),
                "users" => User::count(),
            ],

            "lease_status_pie" => Lease::select(
                'lease_status',
                DB::raw('count(*) as total')
            )->groupBy('lease_status')->pluck('total', 'lease_status'),

            "buildings_by_country" => Building::select(
                'country',
                DB::raw('count(*) as total')
            )->groupBy('country')->pluck('total', 'country'),

            "leases_over_time" => Lease::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month'),
        ]);
    }
}
