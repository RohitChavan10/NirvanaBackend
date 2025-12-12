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
       Schema::create('user_access', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
      ->references('user_id')
      ->on('users')
      ->onDelete('cascade');
            $table->boolean('create')->default(0);
            $table->boolean('view')->default(1);
            $table->boolean('edit')->default(0);
            $table->boolean('delete')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access');
    }
};
