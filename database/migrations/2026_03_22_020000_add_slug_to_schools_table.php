<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add a nullable column first to avoid unique-index conflicts during backfill (if not already added)
        if (!Schema::hasColumn('schools', 'slug')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Backfill slugs from existing names
        $schools = DB::table('schools')->select('id', 'name', 'slug')->get();
        foreach ($schools as $s) {
            if (!empty($s->slug)) {
                continue; // already set
            }
            $base = Str::slug($s->name);
            $slug = $base;
            $i = 1;
            while (DB::table('schools')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }
            DB::table('schools')->where('id', $s->id)->update(['slug' => $slug]);
        }

        // 3) Add a unique index after backfill if it doesn't already exist
        $database = DB::getDatabaseName();
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'schools')
            ->where('column_name', 'slug')
            ->where('index_name', 'schools_slug_unique')
            ->exists();
        if (!$exists) {
            Schema::table('schools', function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
