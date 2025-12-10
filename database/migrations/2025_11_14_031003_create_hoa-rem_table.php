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
        // Create hoa_database table
        Schema::create('hoa_database', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('docket_no', 100)->nullable()->unique('docket_no');
            $table->string('hoa_name')->nullable()->index('idx_hoa_name');
            $table->string('location')->nullable();
            $table->string('status', 100)->nullable();
            $table->double('quantity')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('municipality_id')->nullable()->index('municipality_id');
            $table->integer('province_id')->nullable()->index('province_id');
            $table->string('region')->nullable();

            $table->index(['docket_no'], 'idx_hoa_docket_no');
        });

        // Create rem table
        Schema::create('rem', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('docket_no', 100)->nullable()->index('idx_rem_docket_no');
            $table->string('project_name')->nullable()->index('idx_rem_project_name');
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('status', 100)->nullable()->index('idx_rem_status');
            $table->double('quantity')->nullable();
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_database');
        Schema::dropIfExists('rem');
    }
};
