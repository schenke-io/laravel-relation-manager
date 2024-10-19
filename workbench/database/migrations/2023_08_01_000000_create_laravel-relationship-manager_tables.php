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

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('capitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('country_id');
        });
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('country_id');
        });
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('region_id');
        });
        Schema::create('singles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('highways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('city_highway', function (Blueprint $table) {
            $table->integer('city_id');
            $table->integer('highway_id');
        });
        Schema::create('locations', function (Blueprint $table) {
            $table->integer('locationable_id');
            $table->string('locationable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('singles');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('capitals');
        Schema::dropIfExists('countries');

    }
};
