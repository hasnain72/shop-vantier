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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('financial_status', ['pending', 'authorized', 'partially_paid', 'paid', 'partially_refunded', 'refunded', 'voided'])->default('pending');
            $table->string('fulfillment_status')->nullable();
            $table->string('currency')->default('USD');
            $table->decimal('subtotal_price', 10, 2)->default(0);
            $table->decimal('total_discounts', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('total_shipping', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('total_weight', 10, 3)->default(0);
            $table->boolean('taxes_included')->default(false);
            $table->json('discount_codes')->nullable();
            $table->text('note')->nullable();
            $table->json('tags')->nullable();
            $table->json('note_attributes')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_identifier')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('buyer_accepts_marketing')->default(false);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referring_site')->nullable();
            $table->string('landing_site')->nullable();
            $table->string('cart_token')->nullable();
            $table->string('checkout_token')->nullable();
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('client_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['order_number', 'customer_id', 'financial_status', 'created_at'], 'orders_lookup_idx');
        });

        Schema::create('order_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('title');
            $table->string('variant_title')->nullable();
            $table->string('sku')->nullable();
            $table->string('vendor')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->json('tax_lines')->nullable();
            $table->json('discount_allocations')->nullable();
            $table->boolean('requires_shipping')->default(true);
            $table->boolean('taxable')->default(true);
            $table->boolean('gift_card')->default(false);
            $table->string('name');
            $table->string('fulfillment_service')->default('manual');
            $table->string('fulfillment_status')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        Schema::create('order_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations')->nullOnDelete();
            $table->enum('status', ['pending', 'open', 'success', 'cancelled', 'error', 'failure'])->default('pending');
            $table->string('tracking_company')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->json('tracking_numbers')->nullable();
            $table->json('tracking_urls')->nullable();
            $table->string('shipment_status')->nullable();
            $table->boolean('notify_customer')->default(true);
            $table->json('receipt')->nullable();
            $table->json('line_items')->nullable();
            $table->timestamps();
        });

        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->boolean('restock')->default(false);
            $table->json('refund_line_items')->nullable();
            $table->json('transactions')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->enum('kind', ['authorization', 'capture', 'sale', 'void', 'refund']);
            $table->string('gateway');
            $table->enum('status', ['pending', 'failure', 'success', 'error'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('authorization')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('error_code')->nullable();
            $table->string('message')->nullable();
            $table->string('device_id')->nullable();
            $table->string('source_name')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('order_refunds');
        Schema::dropIfExists('order_fulfillments');
        Schema::dropIfExists('order_line_items');
        Schema::dropIfExists('orders');
    }
};
