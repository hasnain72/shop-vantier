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
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('country');
            $table->string('zip');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_legacy')->default(false);
            $table->boolean('fulfills_online_orders')->default(true);
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->unique()->constrained('product_variants')->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('country_code_of_origin')->nullable();
            $table->string('province_code_of_origin')->nullable();
            $table->string('harmonized_system_code')->nullable();
            $table->boolean('tracked')->default(true);
            $table->boolean('requires_shipping')->default(true);
            $table->timestamps();
        });

        Schema::create('inventory_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('inventory_locations')->cascadeOnDelete();
            $table->integer('available')->default(0);
            $table->integer('incoming')->default(0);
            $table->integer('committed')->default(0);
            $table->timestamps();
            $table->unique(['inventory_item_id', 'location_id']);
        });

        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('inventory_locations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('adjustment');
            $table->enum('reason', ['correction', 'received', 'return', 'damaged', 'theft', 'promotion', 'other']);
            $table->text('note')->nullable();
            $table->integer('available_before');
            $table->integer('available_after');
            $table->timestamps();
        });

        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('target_type', ['line_item', 'shipping_line']);
            $table->enum('target_selection', ['all', 'entitled']);
            $table->enum('allocation_method', ['across', 'each']);
            $table->enum('value_type', ['fixed_amount', 'percentage']);
            $table->decimal('value', 10, 2);
            $table->enum('customer_selection', ['all', 'prerequisite']);
            $table->json('prerequisite_subtotal_range')->nullable();
            $table->json('prerequisite_quantity_range')->nullable();
            $table->json('prerequisite_shipping_price_range')->nullable();
            $table->json('entitled_product_ids')->nullable();
            $table->json('entitled_variant_ids')->nullable();
            $table->json('entitled_collection_ids')->nullable();
            $table->json('prerequisite_customer_ids')->nullable();
            $table->boolean('once_per_customer')->default(false);
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_rule_id')->constrained('price_rules')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
        Schema::dropIfExists('price_rules');
        Schema::dropIfExists('inventory_adjustments');
        Schema::dropIfExists('inventory_levels');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_locations');
    }
};
