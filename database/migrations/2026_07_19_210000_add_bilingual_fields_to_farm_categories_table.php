<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_categories', function (Blueprint $table) {
            $table->string('name_en', 100)->nullable()->after('name');
            $table->string('name_bn', 100)->nullable()->after('name_en');
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_bn')->nullable()->after('description_en');
        });

        DB::table('farm_categories')
            ->whereNull('name_en')
            ->update(['name_en' => DB::raw('name')]);

        DB::table('farm_categories')
            ->whereNull('description_en')
            ->whereNotNull('description')
            ->update(['description_en' => DB::raw('description')]);
    }

    public function down(): void
    {
        Schema::table('farm_categories', function (Blueprint $table) {
            $table->dropColumn([
                'name_en',
                'name_bn',
                'description_en',
                'description_bn',
            ]);
        });
    }
};
