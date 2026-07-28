<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->date('record_date');
            $table->string('type', 30)->default('medicine')->index();
            $table->string('medicine_name', 150)->nullable();
            $table->string('supplier_name', 150)->nullable();
            $table->string('dosage', 150)->nullable();
            $table->string('purpose', 150)->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->date('next_due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id', 'record_date']);
            $table->index('record_date');
            $table->index('next_due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_records');
    }
};
