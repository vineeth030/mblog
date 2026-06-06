<?php

use App\Models\Author;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Backfill existing authors with a unique slug derived from the name.
        $used = [];
        Author::query()->orderBy('id')->get()->each(function (Author $author) use (&$used) {
            $base = Str::slug($author->name) ?: 'author';
            $slug = $base;
            $n = 2;
            while (in_array($slug, $used, true)) {
                $slug = "{$base}-{$n}";
                $n++;
            }
            $used[] = $slug;
            $author->updateQuietly(['slug' => $slug]);
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
