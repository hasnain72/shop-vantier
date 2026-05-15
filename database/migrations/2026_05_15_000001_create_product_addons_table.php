<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('cost_per_item', 10, 2)->nullable();
            $table->integer('inventory_quantity')->default(0);
            $table->enum('inventory_policy', ['deny', 'continue'])->default('deny');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('addon_id')->nullable()->constrained('product_addons')->nullOnDelete();
            $table->decimal('addon_price', 10, 2)->default(0.00)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['addon_id']);
            $table->dropColumn(['addon_id', 'addon_price']);
        });

        Schema::dropIfExists('product_addons');
    }
};
