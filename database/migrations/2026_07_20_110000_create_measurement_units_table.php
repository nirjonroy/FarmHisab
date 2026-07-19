<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 100)->nullable();
            $table->string('name_bn', 100)->nullable();
            $table->string('short_name_en', 30)->nullable();
            $table->string('short_name_bn', 30)->nullable();
            $table->string('code', 30)->unique();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_units');
    }
};
