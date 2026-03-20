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
        Schema::table('lease_expenses', function (Blueprint $table) {

            if (!Schema::hasColumn('lease_expenses', 'vendor_name')) {
                $table->string('vendor_name')->nullable();
            }

            if (!Schema::hasColumn('lease_expenses', 'account_code')) {
                $table->string('account_code')->nullable();
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('lease_expenses', function (Blueprint $table) {

            if (Schema::hasColumn('lease_expenses', 'vendor_name')) {
                $table->dropColumn('vendor_name');
            }

            if (Schema::hasColumn('lease_expenses', 'account_code')) {
                $table->dropColumn('account_code');
            }
        });
    }
};
