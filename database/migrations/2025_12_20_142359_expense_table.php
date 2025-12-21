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
         Schema::create('lease_expenses', function (Blueprint $table) {
            $table->id('expense_id'); // PK
            $table->unsignedBigInteger('lease_id')->nullable(); // FK to leases.id
            $table->unsignedBigInteger('building_id')->nullable(); // FK to buildings.id

            $table->string('expense_year')->nullable();
            $table->string('expense_period')->nullable(); // e.g., "Monthly", "Quarterly", "Annual"
            $table->string('expense_category')->nullable(); // Rent / Non-Rent
            $table->string('expense_type')->nullable(); // Tax, Utilities, Insurance, etc.
            $table->string('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->nullable(); // Pending / Approved / Paid
            $table->string('document_url')->nullable();

            $table->string('is_escalable')->nullable(); // Yes / No
            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('lease_expenses');
    }
};
