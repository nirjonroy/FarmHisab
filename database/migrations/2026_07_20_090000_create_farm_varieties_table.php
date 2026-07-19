<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_category_id')->constrained('farm_categories')->restrictOnDelete();
            $table->string('name_en', 120)->nullable();
            $table->string('name_bn', 120)->nullable();
            $table->string('slug', 150)->unique();
            $table->string('code', 50)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['farm_category_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_varieties');
    }
};
