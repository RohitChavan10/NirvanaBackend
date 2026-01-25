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
        Schema::create('lease_documents', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('lease_id');
    $table->string('file_name')->nullable();
    $table->string('file_path')->nullable(); // /storage/lease_docs/xxx.pdf
    $table->string('file_type')->nullable();
    $table->unsignedBigInteger('uploaded_by')->nullable();
    $table->timestamps();

    $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_documents');
    }
};
