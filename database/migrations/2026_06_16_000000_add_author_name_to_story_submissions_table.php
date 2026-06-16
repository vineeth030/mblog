<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('story_submissions', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('story_submissions', function (Blueprint $table) {
            $table->dropColumn('author_name');
        });
    }
};
