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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->integer('municipality_id', true);
            $table->string('municipality_name')->index('idx_municipality_name');
            $table->integer('province_id')->nullable()->index('province_id');
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->integer('province_id', true);
            $table->string('province_name')->index('idx_province_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('provinces');
    }
};
