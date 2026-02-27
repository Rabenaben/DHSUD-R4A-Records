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
        Schema::table('rem', function (Blueprint $table) {
            $table->integer('province_id')->nullable()->index('idx_rem_province_id')->after('province');
            $table->integer('municipality_id')->nullable()->index('idx_rem_municipality_id')->after('municipality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rem', function (Blueprint $table) {
            $table->dropColumn('province_id');
            $table->dropColumn('municipality_id');
        });
    }
};
