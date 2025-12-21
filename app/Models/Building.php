<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    // Optional: explicitly define table name
    protected $table = 'buildings';

    // Mass assignable fields
    protected $fillable = [
        'system_building_id',
        'sio',
        'building_name',
        'address_1',
        'city',
        'zip_code',
        'country',
        'clli',
        'building_type',
        'building_rentable_area',
        'building_measure_units',
        'latitude',
        'longitude',
        'geocode_latitude',
        'geocode_longitude',
        'building_images',
        'building_status',
        'purchase_price',
        'currency_type',
        'construction_year',
        'last_renovation_year',
        'portfolio',
        'portfolio_sub_group',
        'ownership_type',
        'managed_by',
    ];

    // If you plan to store images as JSON array later
    // protected $casts = [
    //     'building_images' => 'array',
    // ];

    /**
     * Relationships
     *
     * A building can have multiple leases.
     */
    public function leases()
    {
        return $this->hasMany(Lease::class, 'system_building_id', 'system_building_id');
    }
}
