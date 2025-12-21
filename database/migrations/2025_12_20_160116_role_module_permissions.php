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
       Schema::create('role_module_permissions', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('role_id');
    $table->unsignedBigInteger('module_id');
    $table->unsignedBigInteger('permission_id');

    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
    $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
    $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

    $table->unique(['role_id', 'module_id', 'permission_id']);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_module_permissions');
    }
};
