<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountInvoice extends Model
{
     protected $fillable = [
        'expense_id',
        'invoice_number',
        'invoice_date',
        'amount',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    public function expense()
    {
        return $this->belongsTo(LeaseExpense::class, 'expense_id');
    }
}
