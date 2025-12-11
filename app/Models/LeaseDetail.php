<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaseDetail extends Model
{
     protected $fillable = [
        'building_id',
        'building_uid',
        'lease_contract',
        'clauses_acts',
        'clauses_duration',
        'clauses_penalties',
        'contact_details',
        'history'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
