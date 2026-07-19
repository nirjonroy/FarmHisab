<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_category_id')->constrained('farm_categories')->restrictOnDelete();
            $table->foreignId('measurement_unit_id')->constrained('measurement_units')->restrictOnDelete();
            $table->string('name_en', 150)->nullable();
            $table->string('name_bn', 150)->nullable();
            $table->string('sku', 60)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('usage_type', 30)->default('both')->index();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_stock_tracked')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
