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
        Schema::table('hoa_database', function (Blueprint $table) {
            if (!Schema::hasColumn('hoa_database', 'files')) {
                $table->json('files')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_database', function (Blueprint $table) {
            if (Schema::hasColumn('hoa_database', 'files')) {
                $table->dropColumn('files');
            }
        });
    }
};
