<?php

namespace App\Services;

use App\Models\WorkflowLog;
use Illuminate\Support\Facades\Auth;

class WorkflowService
{
    public static function log(array $data)
    {
        return WorkflowLog::create([
            'building_id' => $data['building_id'] ?? null,
            'lease_id'    => $data['lease_id'] ?? null,
            'expense_id'  => $data['expense_id'] ?? null,
            'user_id'     => Auth::id(),
            'role'        => Auth::user()->roles->first()->code ?? 'UNKNOWN',
            'status'      => $data['status'],
            'notes'       => $data['notes'] ?? null,
            'stage_order' => $data['stage_order'] ?? 1,
        ]);
    }
}
