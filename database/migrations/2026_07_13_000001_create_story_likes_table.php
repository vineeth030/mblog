<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Denormalised counter on the post itself, mirroring `views`, so
        // listings and the article page never need a COUNT query.
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('likes')->default(0)->after('views');
        });

        // One row per (post, anonymous visitor). The unique index enforces
        // "one like per visitor per post" at the database level, so concurrent
        // requests can never double-count.
        Schema::create('story_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_hash', 64);
            $table->timestamps();

            $table->unique(['blog_post_id', 'visitor_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_likes');

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('likes');
        });
    }
};
