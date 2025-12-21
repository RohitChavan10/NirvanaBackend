<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\Building;
use Illuminate\Database\Seeder;

class LeaseSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = Building::all();

        for ($i = 1; $i <= 5; $i++) {
            Lease::create([
                'client_lease_id' => 'CL-00' . $i,
                'building_id' => $buildings->random()->id, // pick a random building
                'ownership_type' => 'Leased',
                'system_lease_id' => 'SL-100' . $i,
                'tenant_legal_name' => 'Tenant Company ' . $i,
                'landlord_legal_name' => 'Nirvana Properties',
                'legacy_entity_name' => 'Legacy Entity ' . $i,
                'deed_of_grant' => 'Yes',
                'within_landlord_tenant_act' => 'Yes',
                'lease_clauses' => 'Standard commercial clauses',
                'lease_acts' => 'Local Lease Act',
                'lease_penalties' => 'Late payment penalty applies',
                'lease_hierarchy' => 'Master Lease',
                'lease_agreement_date' => '2023-01-01',
                'possession_date' => '2023-02-01',
                'rent_commencement_date' => '2023-03-01',
                'current_commencement_date' => '2023-03-01',
                'termination_date' => '2028-02-28',
                'current_term' => '5 Years',
                'current_term_remaining' => '4 Years',
                'lease_status' => 'Active',
                'lease_possible_expiration' => '2028-02-28',
                'lease_type' => 'Commercial',
                'lease_recovery_type' => 'Triple Net',
                'lease_rentable_area' => '10000',
                'measure_units' => 'SQFT',
                'primary_use' => 'Office',
                'additional_use' => 'Storage',
                'account_type' => 'Expense Recovery',
                'escalation_type' => 'Annual 5%',
                'security_deposit_type' => 'Cash',
                'security_deposit_amount' => '50000',
                'security_deposit_deposited_date' => '2023-01-15',
                'security_deposit_return_date' => '2028-03-15',
                'portfolio' => 'Portfolio A',
                'portfolio_sub_group' => 'Group 1',
                'lease_version' => '1.0',
                'critical_lease' => 'No',
                'compliance_status' => 'Compliant',
                'lease_source' => 'Seeded',
                'remarks' => 'Initial lease setup',
            ]);
        }
    }
}
