<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('views')->default(0)->after('publish_status');
            // Supports the "most read" ordering and keeps it fast on large tables.
            $table->index(['publish_status', 'views'], 'blog_posts_published_views_index');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex('blog_posts_published_views_index');
            $table->dropColumn('views');
        });
    }
};
