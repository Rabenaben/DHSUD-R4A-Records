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
        Schema::create('borrowers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('region', 100)->nullable();
            $table->string('borrower_name')->index('idx_borrower_name');
            $table->text('remarks')->nullable();
            $table->date('date_borrowed')->nullable();
            $table->date('date_returned')->nullable();
            $table->string('docket_number', 100)->nullable()->index('idx_borrower_docket');
            $table->integer('status_id')->nullable()->index('idx_borrower_status');
        });

        Schema::create('record_status', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('status_name', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowers');
        Schema::dropIfExists('record_status');
    }
};
