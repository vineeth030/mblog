<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('story_submissions', function (Blueprint $table) {
            $table->longText('story_content')->nullable()->after('tags');
        });

        Schema::table('story_submissions', function (Blueprint $table) {
            $table->dropColumn('pdf_file');
        });
    }

    public function down(): void
    {
        Schema::table('story_submissions', function (Blueprint $table) {
            $table->string('pdf_file')->nullable()->after('tags');
        });

        Schema::table('story_submissions', function (Blueprint $table) {
            $table->dropColumn('story_content');
        });
    }
};
