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
        Schema::table('account_invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable();
            $table->string('issued_by')->nullable();   // Vendor name
            $table->string('issued_to')->nullable();   // Your company / building
               // 💰 Amounts
             $table->decimal('subtotal_amount', 12, 2)->nullable(); // before tax
             $table->decimal('tax_amount', 12, 2)->nullable();      // total tax
             $table->decimal('total_amount', 12, 2)->nullable();    // final amount

            // 🇮🇳 Tax Breakdown (keep simple)
            $table->decimal('gst_amount', 12, 2)->nullable();
             // (Skip VAT & Service Tax unless you really need legacy support)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_invoices', function (Blueprint $table) {
           // Drop only if exists (safe rollback)

            if (Schema::hasColumn('account_invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }

            if (Schema::hasColumn('account_invoices', 'issued_by')) {
                $table->dropColumn('issued_by');
            }

            if (Schema::hasColumn('account_invoices', 'issued_to')) {
                $table->dropColumn('issued_to');
            }

            if (Schema::hasColumn('account_invoices', 'subtotal_amount')) {
                $table->dropColumn('subtotal_amount');
            }

            if (Schema::hasColumn('account_invoices', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }

            if (Schema::hasColumn('account_invoices', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('account_invoices', 'gst_amount')) {
                $table->dropColumn('gst_amount');
            }
        });
    }
};
