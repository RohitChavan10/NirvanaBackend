<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    // Optional: explicitly define table name
    protected $table = 'leases';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'client_lease_id',
        'system_building_id',
        'ownership_type',
        'system_lease_id',
        'tenant_legal_name',
        'landlord_legal_name',
        'legacy_entity_name',
        'deed_of_grant',
        'within_landlord_tenant_act',
        'lease_clauses',
        'lease_acts',
        'lease_penalties',
        'lease_hierarchy',
        'lease_agreement_date',
        'possession_date',
        'rent_commencement_date',
        'current_commencement_date',
        'termination_date',
        'current_term',
        'current_term_remaining',
        'lease_status',
        'lease_possible_expiration',
        'lease_type',
        'lease_recovery_type',
        'lease_rentable_area',
        'measure_units',
        'primary_use',
        'additional_use',
        'account_type',
        'escalation_type',
        'security_deposit_type',
        'security_deposit_amount',
        'security_deposit_deposited_date',
        'security_deposit_return_date',
        'portfolio',
        'portfolio_sub_group',
        'lease_version',
        'parent_lease_id',
        'critical_lease',
        'compliance_status',
        'lease_source',
        'remarks',
    ];

    /**
     * Relationships
     */

    // Lease belongs to a Building
    public function building()
    {
        return $this->belongsTo(Building::class, 'system_building_id', 'system_building_id');
    }

      public function documents()
    {
        return $this->hasMany(LeaseDocument::class);
    }
}
