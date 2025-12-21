<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowLog extends Model
{
    protected $table = 'workflow_logs';

    protected $fillable = [
        'building_id',
        'lease_id',
        'expense_id',
        'user_id',
        'role',
        'status',
        'notes',
        'stage_order',
    ];

    /**
     * Relationships
     */

    // The user who performed the action
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship to Building
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    // Relationship to Lease
    public function lease()
    {
        return $this->belongsTo(Lease::class, 'lease_id', 'id');
    }

    // Relationship to LeaseExpense
    public function expense()
    {
        return $this->belongsTo(LeaseExpense::class, 'expense_id', 'expense_id');
    }
}
