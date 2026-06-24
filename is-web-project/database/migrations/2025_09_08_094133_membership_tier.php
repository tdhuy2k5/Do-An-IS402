<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->string('tier_id', 50)->primary();
            $table->string('tier_name', 100)->notNullable();
            $table->unsignedInteger('min_points')->nullable();
            $table->unsignedInteger('max_points')->nullable();
            $table->unsignedInteger('points_per_dollar')->nullable();
            $table->unsignedInteger('accessory_discount_percent')->default(0);
            $table->unsignedInteger('care_plus_discount_percent')->default(0);
            $table->unsignedInteger('haul_away_discount_percent')->default(0);
            $table->unsignedInteger('free_care_plus_years')->default(0);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
