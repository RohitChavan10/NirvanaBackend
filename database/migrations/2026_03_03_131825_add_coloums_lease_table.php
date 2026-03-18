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
        Schema::table('leases', function (Blueprint $table) {
            // Lease Administrator (FK to users table)
             $table->unsignedBigInteger('lease_administrator_id')->nullable();

    $table->foreign('lease_administrator_id')
          ->references('user_id')
          ->on('users')
          ->nullOnDelete();

            // Permitted Use (legal allowed use under lease)
            $table->string('permitted_use')->nullable();

            // Break Option
            $table->string('has_break_option')->default(false);
            $table->string('break_option_date')->nullable();
            $table->string('break_notice_period')->nullable(); // in months

            // Rent Review
            $table->string('next_rent_review_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
              $table->dropForeign(['lease_administrator_id']);
            $table->dropColumn([
                'lease_administrator_id',
                'permitted_use',
                'has_break_option',
                'break_option_date',
                'break_notice_period',
                'next_rent_review_date',
            ]);
        });
    }
};
