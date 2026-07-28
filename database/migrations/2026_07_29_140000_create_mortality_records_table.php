<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mortality_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->date('record_date');
            $table->string('type', 30)->default('mortality')->index();
            $table->unsignedInteger('birds');
            $table->string('cause', 150)->nullable();
            $table->string('action_taken', 150)->nullable();
            $table->string('reported_by', 150)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id', 'record_date']);
            $table->index('record_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mortality_records');
    }
};
