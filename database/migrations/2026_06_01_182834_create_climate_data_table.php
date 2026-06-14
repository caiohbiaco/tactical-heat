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
        Schema::create('climate_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_id')->constrained()->OnDelete('cascade');
            $table->tinyInteger('month');
            $table->decimal('temp_max_avg', 5, 2);
            $table->decimal('temp_min_avg', 5, 2);
            $table->decimal('humidity_avg', 5, 2);
            $table->decimal('heat_index_avg', 5, 2);
            $table->enum('risk_level',['low', 'medium', 'high']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_data');
    }
};
