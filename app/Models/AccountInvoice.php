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
        'due_date',
        'issued_by',
        'issued_to',
        'subtotal_amount',
        'tax_amount',
        'total_amount',
        'gst_amount',
    ];

    public function expense()
    {
        return $this->belongsTo(LeaseExpense::class, 'expense_id');
    }
}
