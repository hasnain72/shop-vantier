<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->as('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('collections', \App\Http\Controllers\Admin\CollectionController::class)
            ->except(['show'])
            ->names('collections');

        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)
            ->except(['show'])
            ->names('products');

        // Settings
        Route::prefix('settings')->as('settings.')->group(function () {
            Route::get('payments',  [\App\Http\Controllers\Admin\Settings\PaymentSettingsController::class, 'index'])->name('payments');
            Route::put('payments',  [\App\Http\Controllers\Admin\Settings\PaymentSettingsController::class, 'update'])->name('payments.update');
        });

        // Orders
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->except(['show']);
        Route::get('orders/{order}',        [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/cancel',    [\App\Http\Controllers\Admin\OrderController::class, 'cancelOrder'])->name('orders.cancel');
        Route::post('orders/{order}/fulfill',   [\App\Http\Controllers\Admin\OrderController::class, 'fulfillOrder'])->name('orders.fulfill');
        Route::post('orders/{order}/mark-paid', [\App\Http\Controllers\Admin\OrderController::class, 'markAsPaid'])->name('orders.mark-paid');
        Route::post('orders/{order}/archive',   [\App\Http\Controllers\Admin\OrderController::class, 'archiveOrder'])->name('orders.archive');
        Route::post('orders/{order}/unarchive', [\App\Http\Controllers\Admin\OrderController::class, 'unarchiveOrder'])->name('orders.unarchive');
        Route::get('orders/{order}/print',      [\App\Http\Controllers\Admin\OrderController::class, 'printOrder'])->name('orders.print');
        Route::get('orders/{order}/refunds/create', [\App\Http\Controllers\Admin\RefundController::class, 'create'])->name('orders.refunds.create');
        Route::post('orders/{order}/refunds',       [\App\Http\Controllers\Admin\RefundController::class, 'store'])->name('orders.refunds.store');

        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)
            ->except(['show'])
            ->names('customers');
        Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        Route::post('customers/{customer}/invite',     [\App\Http\Controllers\Admin\CustomerController::class, 'sendInvite'])->name('customers.invite');
        Route::post('customers/{customer}/deactivate', [\App\Http\Controllers\Admin\CustomerController::class, 'deactivate'])->name('customers.deactivate');
        Route::post('customers/{customer}/activate',   [\App\Http\Controllers\Admin\CustomerController::class, 'activate'])->name('customers.activate');

        // Shipping
        Route::get('shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('shipping/zones',          [\App\Http\Controllers\Admin\ShippingController::class, 'storeZone'])->name('shipping.zones.store');
        Route::put('shipping/zones/{zone}',    [\App\Http\Controllers\Admin\ShippingController::class, 'updateZone'])->name('shipping.zones.update');
        Route::delete('shipping/zones/{zone}', [\App\Http\Controllers\Admin\ShippingController::class, 'deleteZone'])->name('shipping.zones.delete');
        Route::post('shipping/zones/{zone}/rates',  [\App\Http\Controllers\Admin\ShippingController::class, 'storeRate'])->name('shipping.rates.store');
        Route::put('shipping/rates/{rate}',         [\App\Http\Controllers\Admin\ShippingController::class, 'updateRate'])->name('shipping.rates.update');
        Route::delete('shipping/rates/{rate}',      [\App\Http\Controllers\Admin\ShippingController::class, 'deleteRate'])->name('shipping.rates.delete');

        // Inventory
        Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/adjust',   [\App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('inventory/transfer', [\App\Http\Controllers\Admin\InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::get('inventory/{item}/history', [\App\Http\Controllers\Admin\InventoryController::class, 'history'])->name('inventory.history');

        // Locations
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class)->names('locations');

        Route::prefix('products/{product}')->as('products.')->group(function () {
            Route::get('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'index'])->name('variants.index');
            Route::get('variants/create', [\App\Http\Controllers\Admin\ProductVariantController::class, 'create'])->name('variants.create');
            Route::post('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'store'])->name('variants.store');
            Route::get('variants/{variant}/edit', [\App\Http\Controllers\Admin\ProductVariantController::class, 'edit'])->name('variants.edit');
            Route::put('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'update'])->name('variants.update');
            Route::delete('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'destroy'])->name('variants.destroy');
        });
    });
});
