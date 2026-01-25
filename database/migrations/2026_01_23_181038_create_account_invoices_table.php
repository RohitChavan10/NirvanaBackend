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
        Schema::create('account_invoices', function (Blueprint $table) {
           $table->id();
    $table->unsignedBigInteger('expense_id'); // or account_id
    $table->string('invoice_number')->nullable();
    $table->date('invoice_date')->nullable();
    $table->decimal('amount', 12, 2)->nullable();
    $table->string('file_name');
    $table->string('file_path'); // /storage/invoices/xxx.pdf
    $table->unsignedBigInteger('uploaded_by')->nullable();
    $table->timestamps();

    $table->foreign('expense_id')->references('expense_id')->on('lease_expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_invoices');
    }
};
