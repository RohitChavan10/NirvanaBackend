<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{

    // Optional: explicitly define table name
    protected $table = 'certificates';

    // Mass assignable fields
    protected $fillable = [
        'building_id',
        'certificate_number',
        'certificate_name',
        'certificate_type',
        'owner_name',
        'owner_address',
        'issued_by',
        'approved_by',
        'issued_date',
        'expiry_date',
        'status',
        'file_path',
        'notes',
    ];

     /**
     * Relationships
     *
     * A certificate belongs to a building.
     */

   public function building()
{
    return $this->belongsTo(Building::class, 'building_id', 'id');
}
}
