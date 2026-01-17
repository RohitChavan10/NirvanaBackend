<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaseExpense extends Model
{
    protected $primaryKey = 'expense_id';
      protected $table = 'lease_expenses';

    protected $fillable = [
        'lease_id',
        'building_id',
        'expense_year',
        'expense_period',
        'expense_category',
        'expense_type',
        'amount',
        'currency',
        'status',
        'document_url',
        'is_escalable',
        'note',
    ];

    /**
     * Relationships
     */

    // Expense belongs to a Lease
    public function lease()
    {
        return $this->belongsTo(Lease::class, 'lease_id', 'id');
    }

    // Expense belongs to a Building
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }
}
