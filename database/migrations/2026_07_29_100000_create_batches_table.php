<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no', 30)->unique();
            $table->string('batch_name', 150);
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();
            $table->foreignId('bird_type_id')->constrained('farm_categories')->restrictOnDelete();
            $table->foreignId('breed_id')->constrained('farm_varieties')->restrictOnDelete();
            $table->string('supplier_name', 150)->nullable();
            $table->date('purchase_date');
            $table->date('arrival_date')->nullable();
            $table->unsignedInteger('initial_birds');
            $table->decimal('purchase_price_per_bird', 12, 2)->default(0);
            $table->decimal('total_purchase_cost', 14, 2)->default(0);
            $table->decimal('expected_market_weight', 8, 3)->nullable();
            $table->unsignedInteger('expected_market_age')->nullable();
            $table->decimal('feed_target_bags', 10, 2)->nullable();
            $table->decimal('medicine_budget', 12, 2)->default(0);
            $table->decimal('other_budget', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('purchase_date');
            $table->index('arrival_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
