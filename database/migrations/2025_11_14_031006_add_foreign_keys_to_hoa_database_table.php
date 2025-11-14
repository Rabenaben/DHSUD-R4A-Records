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
            $table->foreign(['municipality_id'], 'hoa_database_ibfk_1')->references(['municipality_id'])->on('municipalities')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['province_id'], 'hoa_database_ibfk_2')->references(['province_id'])->on('provinces')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_database', function (Blueprint $table) {
            $table->dropForeign('hoa_database_ibfk_1');
            $table->dropForeign('hoa_database_ibfk_2');
        });
    }
};
