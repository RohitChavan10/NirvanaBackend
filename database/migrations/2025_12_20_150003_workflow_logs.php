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
      Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();

            // Foreign keys to actual tables
            $table->unsignedBigInteger('building_id')->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');

            $table->unsignedBigInteger('lease_id')->nullable();
            $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');

            $table->unsignedBigInteger('expense_id')->nullable();
            $table->foreign('expense_id')->references('expense_id')->on('lease_expenses')->onDelete('cascade');

            $table->unsignedBigInteger('user_id'); // the user who acted
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->string('role');   // 'creator', 'reviewer', 'manager', 'admin'
            $table->string('status'); // 'submitted', 'reviewed', 'approved', 'rejected'
            $table->text('notes')->nullable();
            $table->integer('stage_order')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('workflow_logs');
    }
};
