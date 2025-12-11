<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
     protected $fillable = [
        'uid','name','address','city','state','country',
        'managed_by','building_age','status','area','nearest_landmarks',
        'rent','contact_person','history','images','latitude','longitude'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function leaseDetails()
    {
        return $this->hasOne(LeaseDetail::class);
    }
}
