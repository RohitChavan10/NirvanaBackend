<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
     protected $table = 'user_access';

    protected $fillable = [
        'user_id',
        'create',
        'view',
        'edit',
        'delete',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }//
}
