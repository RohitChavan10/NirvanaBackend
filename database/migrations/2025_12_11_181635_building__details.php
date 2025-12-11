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
       Schema::create('buildings', function (Blueprint $table) {
            $table->id();

            $table->string('uid')->unique();
            $table->string('name');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');

            $table->string('managed_by')->nullable();

            $table->integer('building_age')->nullable();

            $table->string('status');

            $table->string('area')->nullable();
            $table->text('nearest_landmarks')->nullable();
            $table->decimal('rent', 12, 2)->nullable();

            $table->string('contact_person')->nullable();
            $table->longText('history')->nullable();

            $table->string('images')->nullable();

            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
