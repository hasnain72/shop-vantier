<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->string('author_ar')->nullable()->after('author');
            $table->text('summary_html_ar')->nullable()->after('summary_html');
            $table->longText('body_html_ar')->nullable()->after('body_html');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'author_ar', 'summary_html_ar', 'body_html_ar']);
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('title_ar');
        });
    }
};
