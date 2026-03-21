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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            // Foreign key (standard Laravel way)
            $table->foreignId('building_id')
                  ->constrained('buildings')
                  ->onDelete('cascade');

            // Certificate details
            $table->string('certificate_number')->nullable();
            $table->string('certificate_name');
            $table->string('certificate_type')->nullable();

            // Owner details
            $table->string('owner_name')->nullable();
            $table->text('owner_address')->nullable();

            // Authority details
            $table->string('issued_by')->nullable();
            $table->string('approved_by')->nullable();

            // Dates
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();

            // Status
            $table->string('status')->nullable();
            // File path
            $table->string('file_path')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
