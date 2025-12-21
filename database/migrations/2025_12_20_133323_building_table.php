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
         Schema::create('buildings', function (Blueprint $table) {
            $table->id(); // PK

            $table->string('system_building_id')->nullable();
            $table->string('sio')->nullable();
            $table->string('building_name')->nullable();

            $table->string('address_1')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();

            $table->string('clli')->nullable();
            $table->string('building_type')->nullable();

            $table->string('building_rentable_area')->nullable();
            $table->string('building_measure_units')->nullable();
            
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            $table->string('geocode_latitude')->nullable();
            $table->string('geocode_longitude')->nullable();
            
            $table->string('building_images')->nullable();
            $table->string('building_status')->nullable();
            
            $table->string('purchase_price')->nullable();
            $table->string('currency_type')->nullable();
            
            $table->string('construction_year')->nullable();
            $table->string('last_renovation_year')->nullable();

            $table->string('portfolio')->nullable();
            $table->string('portfolio_sub_group')->nullable();

            $table->string('ownership_type')->nullable(); // Owned / Leased / Managed
            $table->string('managed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
