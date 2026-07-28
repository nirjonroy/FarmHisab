<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weight_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->date('record_date');
            $table->unsignedInteger('age_days');
            $table->unsignedInteger('sample_birds');
            $table->decimal('average_weight', 8, 3);
            $table->decimal('total_weight', 10, 3);
            $table->decimal('target_weight', 8, 3)->nullable();
            $table->decimal('uniformity_percentage', 5, 2)->nullable();
            $table->string('weighed_by', 150)->nullable();
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
        Schema::dropIfExists('weight_records');
    }
};
