<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->date('sale_date');
            $table->string('buyer_name', 150);
            $table->string('buyer_phone', 50)->nullable();
            $table->unsignedInteger('birds_sold');
            $table->decimal('average_weight', 8, 3)->nullable();
            $table->decimal('total_weight', 10, 3);
            $table->decimal('rate_per_kg', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method', 30)->default('cash')->index();
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id', 'sale_date']);
            $table->index('sale_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
