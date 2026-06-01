<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // display / original name
            $table->string('file_name')->unique();           // stored filename on disk
            $table->string('mime_type');
            $table->unsignedBigInteger('size');              // bytes
            $table->string('disk')->default('public');
            $table->string('path');                          // relative: gallery/abc123.jpg
            $table->string('alt')->nullable();
            $table->string('folder')->nullable()->default('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
