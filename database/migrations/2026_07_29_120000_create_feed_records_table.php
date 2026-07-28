<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->date('record_date');
            $table->string('feed_name', 150)->nullable();
            $table->string('supplier_name', 150)->nullable();
            $table->decimal('bags', 10, 2)->default(0);
            $table->decimal('weight_per_bag', 10, 2)->default(50);
            $table->decimal('quantity_kg', 12, 2)->default(0);
            $table->decimal('unit_price_per_bag', 12, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
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
        Schema::dropIfExists('feed_records');
    }
};
