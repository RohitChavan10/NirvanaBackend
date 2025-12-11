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
          Schema::create('lease_details', function (Blueprint $table) {
            $table->id();

            // Foreign key reference to buildings table
            $table->unsignedBigInteger('building_id');
            $table->string('building_uid'); // storing UID as requested

            $table->longText('lease_contract')->nullable();

            // Clauses
            $table->longText('clauses_acts')->nullable();
            $table->string('clauses_duration')->nullable();
            $table->longText('clauses_penalties')->nullable();

            $table->text('contact_details')->nullable();
            $table->longText('history')->nullable();

            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('building_id')
                  ->references('id')
                  ->on('buildings')
                  ->onDelete('cascade'); // If building deleted → leases deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_details');
    }
};
