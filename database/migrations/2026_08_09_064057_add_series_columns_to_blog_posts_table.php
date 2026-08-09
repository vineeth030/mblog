<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // Points at the id of the series' first/root part. The root part
            // points at itself once a series is formed, so `where series_id
            // = X` returns every part — including the root — in one query.
            $table->foreignId('series_id')->nullable()->after('author_id')->constrained('blog_posts')->nullOnDelete();
            $table->unsignedInteger('part_number')->nullable()->after('series_id');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn(['series_id', 'part_number']);
        });
    }
};
