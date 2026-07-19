<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farm_categories', function (Blueprint $table) {
            $table->string('activity_type', 30)->default('production')->index()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('farm_categories', function (Blueprint $table) {
            $table->dropIndex(['activity_type']);
            $table->dropColumn('activity_type');
        });
    }
};
