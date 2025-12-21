<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id(); // PK

            $table->string('client_lease_id')->nullable();
            $table->unsignedBigInteger('building_id')->nullable(); // FK reference to buildings.id
            $table->string('ownership_type')->nullable();

            $table->string('system_lease_id')->nullable();
            $table->string('tenant_legal_name')->nullable();
            $table->string('landlord_legal_name')->nullable();
            $table->string('legacy_entity_name')->nullable();

            $table->string('deed_of_grant')->nullable();
            $table->string('within_landlord_tenant_act')->nullable();

            $table->string('lease_clauses')->nullable();
            $table->string('lease_acts')->nullable();
            $table->string('lease_penalties')->nullable();
            $table->string('lease_hierarchy')->nullable();

            $table->string('lease_agreement_date')->nullable();
            $table->string('possession_date')->nullable();
            $table->string('rent_commencement_date')->nullable();
            $table->string('current_commencement_date')->nullable();
            $table->string('termination_date')->nullable();

            $table->string('current_term')->nullable();
            $table->string('current_term_remaining')->nullable();

            $table->string('lease_status')->nullable();
            $table->string('lease_possible_expiration')->nullable();

            $table->string('lease_type')->nullable();
            $table->string('lease_recovery_type')->nullable();

            $table->string('lease_rentable_area')->nullable();
            $table->string('measure_units')->nullable();

            $table->string('primary_use')->nullable();
            $table->string('additional_use')->nullable();

            $table->string('account_type')->nullable();
            $table->string('escalation_type')->nullable();

            $table->string('security_deposit_type')->nullable();
            $table->string('security_deposit_amount')->nullable();
            $table->string('security_deposit_deposited_date')->nullable();
            $table->string('security_deposit_return_date')->nullable();

            $table->string('portfolio')->nullable();
            $table->string('portfolio_sub_group')->nullable();

            $table->string('lease_version')->nullable();
            $table->string('parent_lease_id')->nullable(); // for renewals / amendments

            $table->string('critical_lease')->nullable(); // Yes / No
            $table->string('compliance_status')->nullable();

            $table->string('lease_source')->nullable(); // Manual / Imported / API
            $table->string('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
