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
        Schema::table('borrowers', function (Blueprint $table) {
            $table->foreign(['status_id'], 'borrowers_ibfk_1')->references(['id'])->on('record_status')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['docket_number'], 'borrowers_ibfk_2')->references(['docket_no'])->on('hoa_database')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropForeign('borrowers_ibfk_1');
            $table->dropForeign('borrowers_ibfk_2');
        });
    }
};
