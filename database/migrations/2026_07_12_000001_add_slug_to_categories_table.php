<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Backfill slugs from the (unique) category names. Unicode-aware so
        // Malayalam names keep descriptive Malayalam slugs (Str::slug would
        // strip non-ASCII, leaving empty slugs).
        $slugify = function (string $value): string {
            $slug = mb_strtolower(trim($value));
            $slug = preg_replace('/[\s_]+/u', '-', $slug);
            $slug = preg_replace('/[^\p{L}\p{N}\p{M}-]+/u', '', $slug);
            $slug = preg_replace('/-+/', '-', $slug);

            return trim($slug, '-') ?: 'category';
        };

        $seen = [];
        foreach (DB::table('categories')->orderBy('id')->get() as $category) {
            $base = $slugify($category->name);
            $slug = $base;
            $n = 2;
            while (in_array($slug, $seen, true)) {
                $slug = "{$base}-{$n}";
                $n++;
            }
            $seen[] = $slug;

            DB::table('categories')->where('id', $category->id)->update(['slug' => $slug]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
