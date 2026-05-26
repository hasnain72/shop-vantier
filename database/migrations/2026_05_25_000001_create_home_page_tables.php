<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_craft_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('description_en');
            $table->text('description_ar');
            $table->string('image_url');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('home_customer_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->text('review_en');
            $table->text('review_ar');
            $table->string('product_image_url');
            $table->string('product_name_en');
            $table->string('product_name_ar');
            $table->string('current_price');
            $table->string('compare_at_price')->nullable();
            $table->string('currency_en')->default('SAR ');
            $table->string('currency_ar')->default('ر.س ');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question_en');
            $table->text('question_ar');
            $table->text('answer_en');
            $table->text('answer_ar');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_workshop_items', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('video_mp4');
            $table->string('video_poster')->nullable();
            $table->string('thumb_url');
            $table->string('current_price');
            $table->string('original_price')->nullable();
            $table->string('currency')->default('$');
            $table->string('product_path')->nullable();
            $table->json('modal_gallery_urls')->nullable();
            $table->json('modal_body_primary_html')->nullable();
            $table->json('modal_body_secondary_html')->nullable();
            $table->json('size_options')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_workshop_items');
        Schema::dropIfExists('home_faqs');
        Schema::dropIfExists('home_customer_reviews');
        Schema::dropIfExists('home_craft_cards');
    }
};
