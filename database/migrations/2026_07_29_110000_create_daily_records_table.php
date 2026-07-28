<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->date('record_date');
            $table->unsignedInteger('opening_birds');
            $table->unsignedInteger('mortality_birds')->default(0);
            $table->unsignedInteger('culled_birds')->default(0);
            $table->unsignedInteger('sold_birds')->default(0);
            $table->unsignedInteger('closing_birds');
            $table->decimal('feed_consumed_bags', 10, 2)->default(0);
            $table->decimal('feed_cost', 12, 2)->default(0);
            $table->decimal('medicine_cost', 12, 2)->default(0);
            $table->decimal('average_weight', 8, 3)->nullable();
            $table->decimal('temperature', 6, 2)->nullable();
            $table->decimal('humidity', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['batch_id', 'record_date']);
            $table->index('record_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_records');
    }
};
