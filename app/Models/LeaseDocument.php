<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaseDocument extends Model
{
     protected $fillable = [
        'lease_id',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_by',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
    public function uploader()
{
    return $this->belongsTo(User::class, 'uploaded_by', 'user_id');
}
}
