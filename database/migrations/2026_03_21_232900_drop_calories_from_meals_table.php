<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            if (Schema::hasColumn('meals', 'calories')) {
                $table->dropColumn('calories');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            if (!Schema::hasColumn('meals', 'calories')) {
                $table->integer('calories')->nullable()->after('description');
            }
        });
    }
};
