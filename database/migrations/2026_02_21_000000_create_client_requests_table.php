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
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type'); // HOA or REM
            $table->string('project_name'); // Name of Project / HOA
            $table->string('docket_no');
            $table->string('location')->nullable();
            $table->string('requested_by');
            $table->string('or_no');
            $table->decimal('amount', 10, 2);
            $table->text('requested_docs')->nullable(); // JSON array of selected documents
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
