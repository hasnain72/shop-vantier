<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('sort_order', ['manual', 'best-selling', 'alpha-asc', 'alpha-desc', 'price-asc', 'price-desc', 'created-asc', 'created-desc'])->default('manual');
            $table->string('template_suffix')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_position')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body_html')->nullable();
            $table->string('vendor')->nullable();
            $table->string('product_type')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['active', 'archived', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('template_suffix')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('options')->nullable();
            $table->json('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('has_only_default_variant')->default(true);
            $table->boolean('requires_shipping')->default(true);
            $table->boolean('taxable')->default(true);
            $table->integer('sort_position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('title');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('cost_per_item', 10, 2)->nullable();
            $table->string('option1')->nullable();
            $table->string('option2')->nullable();
            $table->string('option3')->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->enum('weight_unit', ['kg', 'g', 'lb', 'oz'])->default('kg');
            $table->boolean('requires_shipping')->default(true);
            $table->boolean('taxable')->default(true);
            $table->string('fulfillment_service')->default('manual');
            $table->string('inventory_management')->nullable();
            $table->enum('inventory_policy', ['deny', 'continue'])->default('deny');
            $table->integer('inventory_quantity')->default(0);
            $table->integer('old_inventory_quantity')->default(0);
            $table->unsignedBigInteger('image_id')->nullable();
            $table->integer('position')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['product_id', 'sku']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->json('variant_ids')->nullable();
            $table->string('src');
            $table->string('alt')->nullable();
            $table->integer('position')->default(1);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->timestamps();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')->on('product_images')->nullOnDelete();
        });

        Schema::create('collection_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->unique(['collection_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_product');
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
        });
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('product_types');
    }
};
