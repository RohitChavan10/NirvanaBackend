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
        Schema::create('users', function (Blueprint $table) {
           // custom primary key
        $table->id('user_id');

        // custom user fields
        $table->string('username')->unique();
        $table->string('user_firstName');
        $table->string('user_lastName');
        $table->string('email_id')->unique();
        $table->string('password');
        $table->string('user_type'); // like "admin", "customer", etc.

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
