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
            $table->string('previous_status')->nullable()->after('status');
        });

        Schema::table('rem_database', function (Blueprint $table) {
            $table->string('previous_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_database', function (Blueprint $table) {
            $table->dropColumn('previous_status');
        });

        Schema::table('rem_database', function (Blueprint $table) {
            $table->dropColumn('previous_status');
        });
    }
};
