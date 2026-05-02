<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('slug')->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['category', 'author_name']);
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['author_id']);
            $table->dropColumn(['category_id', 'author_id']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('category')->after('slug');
            $table->string('author_name')->after('category');
        });
    }
};
